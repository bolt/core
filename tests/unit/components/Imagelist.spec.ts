import { mount } from '@vue/test-utils';
import { nextTick } from 'vue';
import { describe, expect, it } from 'vitest';
import Imagelist from '@/editor/Components/Imagelist.vue';

// Image.vue emits its own component instance as the payload, and Imagelist reads
// `fieldName` off it to work out which row moved. The stub reproduces that
// contract without the upload and lightbox machinery.
const imageStub = {
    name: 'EditorImageStub',
    props: {
        name: { type: String, default: '' },
        filename: { type: String, default: '' },
        isFirstInImagelist: { type: Boolean, default: false },
        isLastInImagelist: { type: Boolean, default: false },
        readonly: { type: Boolean, default: false },
        extraFields: { type: Array, default: () => [] },
        extraData: { type: Object, default: () => ({}) },
    },
    computed: {
        fieldName(this: { name: string }) {
            return `${this.name}[]`;
        },
    },
    methods: {
        moveUp(this: { $emit: (e: string, p: unknown) => void }) {
            this.$emit('move-image-up', this);
        },
        moveDown(this: { $emit: (e: string, p: unknown) => void }) {
            this.$emit('move-image-down', this);
        },
        remove(this: { $emit: (e: string, p: unknown) => void }) {
            this.$emit('remove', this);
        },
    },
    template: '<div class="image-stub" :data-name="name">{{ filename }}</div>',
};

const defaultProps = {
    directory: 'images',
    name: 'fields[gallery]',
    filelist: '/bolt/filelist',
    csrfToken: 'token-123',
    labels: { add_new_image: 'Add image' },
    extensions: ['jpg', 'png'],
    attributesLink: '/bolt/attributes',
    limit: 3,
    readonly: false,
    extraFields: [],
};

const mountImagelist = (images: unknown[], props: Record<string, unknown> = {}) =>
    mount(Imagelist, {
        // `images` is mutated in place by data(), so hand each mount its own copy.
        propsData: { ...defaultProps, images: JSON.parse(JSON.stringify(images)), ...props },
        stubs: { 'editor-image': imageStub },
    });

const rows = (wrapper: ReturnType<typeof mount>) => wrapper.findAll('.image-stub').wrappers.map(row => row.text());
const names = (wrapper: ReturnType<typeof mount>) =>
    wrapper.findAll('.image-stub').wrappers.map(row => row.attributes('data-name'));
const child = (wrapper: ReturnType<typeof mount>, index: number) =>
    wrapper.findAllComponents(imageStub).at(index).vm as unknown as {
        moveUp: () => void;
        moveDown: () => void;
        remove: () => void;
    };

const images = [{ filename: 'a.jpg' }, { filename: 'b.jpg' }];

describe('EditorImagelist', () => {
    it('renders an image row per entry', () => {
        const wrapper = mountImagelist(images);

        expect(rows(wrapper)).toEqual(['a.jpg', 'b.jpg']);
    });

    it('indexes each row into the field name', () => {
        const wrapper = mountImagelist(images);

        expect(names(wrapper)).toEqual(['fields[gallery][0]', 'fields[gallery][1]']);
    });

    it('submits an empty value when the list is empty', () => {
        const wrapper = mountImagelist([]);

        const hidden = wrapper.find('input[type="hidden"]');
        expect(hidden.attributes('name')).toBe('fields[gallery]');
        expect((hidden.element as HTMLInputElement).value).toBe('');
    });

    it('passes the extra fields down to each row', () => {
        const extraFields = ['caption', 'credit'];
        const wrapper = mountImagelist(images, { extraFields });

        expect(wrapper.findAllComponents(imageStub).at(0).props('extraFields')).toEqual(extraFields);
    });

    it('tells each row whether it is first or last', () => {
        const wrapper = mountImagelist([{ filename: 'a.jpg' }, { filename: 'b.jpg' }, { filename: 'c.jpg' }]);

        const children = wrapper.findAllComponents(imageStub).wrappers;
        expect(children.map(c => c.props('isFirstInImagelist'))).toEqual([true, false, false]);
        expect(children.map(c => c.props('isLastInImagelist'))).toEqual([false, false, true]);
    });

    it('adds an empty image row', async () => {
        const wrapper = mountImagelist(images);

        await wrapper.find('button').trigger('click');

        expect(rows(wrapper)).toEqual(['a.jpg', 'b.jpg', '']);
    });

    it('stops allowing more once the limit is reached', () => {
        const wrapper = mountImagelist(images, { limit: 2 });

        expect(wrapper.find('button').attributes('disabled')).toBeDefined();
    });

    it('never allows more when read-only', () => {
        const wrapper = mountImagelist(images, { readonly: true });

        expect(wrapper.find('button').attributes('disabled')).toBeDefined();
    });

    it('moves an image down and up', async () => {
        const wrapper = mountImagelist(images);

        child(wrapper, 0).moveDown();
        await nextTick();
        expect(rows(wrapper)).toEqual(['b.jpg', 'a.jpg']);

        child(wrapper, 1).moveUp();
        await nextTick();
        expect(rows(wrapper)).toEqual(['a.jpg', 'b.jpg']);
    });

    it('leaves the ends alone when moving past them', async () => {
        const wrapper = mountImagelist(images);

        child(wrapper, 1).moveDown();
        child(wrapper, 0).moveUp();
        await nextTick();

        expect(rows(wrapper)).toEqual(['a.jpg', 'b.jpg']);
    });

    it('hides a removed image rather than dropping it', async () => {
        // Removal is a soft delete: the entry stays in the list so its indexes
        // keep lining up with the posted form field names, and is only hidden
        // from view.
        const wrapper = mountImagelist(images);

        child(wrapper, 0).remove();
        await nextTick();

        expect(rows(wrapper)).toEqual(['b.jpg']);
        expect(names(wrapper)).toEqual(['fields[gallery][1]']);
    });

    it('counts only visible images against the limit', async () => {
        const wrapper = mountImagelist(images, { limit: 2 });
        expect(wrapper.find('button').attributes('disabled')).toBeDefined();

        child(wrapper, 0).remove();
        await nextTick();

        expect(wrapper.find('button').attributes('disabled')).toBeUndefined();
    });

    it('works out first and last from the visible images only', async () => {
        const wrapper = mountImagelist([{ filename: 'a.jpg' }, { filename: 'b.jpg' }, { filename: 'c.jpg' }]);

        child(wrapper, 2).remove();
        await nextTick();

        const children = wrapper.findAllComponents(imageStub).wrappers;
        expect(children.map(c => c.props('isLastInImagelist'))).toEqual([false, true]);
    });

    it('shows the empty-value input again once every image is removed', async () => {
        const wrapper = mountImagelist([{ filename: 'a.jpg' }]);
        expect(wrapper.find('input[type="hidden"]').exists()).toBe(false);

        child(wrapper, 0).remove();
        await nextTick();

        expect(wrapper.find('input[type="hidden"]').exists()).toBe(true);
    });

    it('addresses the right row past the tenth', async () => {
        const many = Array.from({ length: 12 }, (_, index) => ({ filename: `${index}.jpg` }));
        const wrapper = mountImagelist(many, { limit: 20 });

        child(wrapper, 10).remove();
        await nextTick();

        expect(rows(wrapper)).not.toContain('10.jpg');
        expect(rows(wrapper)).toHaveLength(11);
    });
});
