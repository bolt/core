import { mount } from '@vue/test-utils';
import { nextTick } from 'vue';
import { describe, expect, it } from 'vitest';
import Filelist from '@/editor/Components/Filelist.vue';

// File.vue emits its own component instance as the payload, and Filelist reads
// `fieldName` off it to work out which row moved. The stub reproduces that
// contract without dragging in axios, Bootstrap and the upload machinery.
const fileStub = {
    name: 'EditorFileStub',
    props: {
        name: { type: String, default: '' },
        filename: { type: String, default: '' },
        isFirstInFilelist: { type: Boolean, default: false },
        isLastInFilelist: { type: Boolean, default: false },
        readonly: { type: Boolean, default: false },
    },
    computed: {
        fieldName(this: { name: string }) {
            return `${this.name}[]`;
        },
    },
    methods: {
        moveUp(this: { $emit: (e: string, p: unknown) => void }) {
            this.$emit('move-file-up', this);
        },
        moveDown(this: { $emit: (e: string, p: unknown) => void }) {
            this.$emit('move-file-down', this);
        },
        remove(this: { $emit: (e: string, p: unknown) => void }) {
            this.$emit('remove', this);
        },
    },
    template: '<div class="file-stub" :data-name="name">{{ filename }}</div>',
};

const labels = { add_new_file: 'Add file' };

const defaultProps = {
    directory: 'files',
    name: 'fields[documents]',
    filelist: '/bolt/filelist',
    csrfToken: 'token-123',
    labels,
    extensions: ['pdf', 'txt'],
    attributesLink: '/bolt/attributes',
    limit: 3,
    readonly: false,
};

const mountFilelist = (files: unknown[], props: Record<string, unknown> = {}) =>
    mount(Filelist, {
        // `files` is mutated in place by data(), so hand each mount its own copy.
        propsData: { ...defaultProps, files: JSON.parse(JSON.stringify(files)), ...props },
        stubs: { 'editor-file': fileStub },
    });

const rows = (wrapper: ReturnType<typeof mount>) => wrapper.findAll('.file-stub').wrappers.map(row => row.text());
const names = (wrapper: ReturnType<typeof mount>) =>
    wrapper.findAll('.file-stub').wrappers.map(row => row.attributes('data-name'));
const child = (wrapper: ReturnType<typeof mount>, index: number) =>
    wrapper.findAllComponents(fileStub).at(index).vm as unknown as {
        moveUp: () => void;
        moveDown: () => void;
        remove: () => void;
    };

const files = [{ filename: 'a.pdf' }, { filename: 'b.pdf' }];

describe('EditorFilelist', () => {
    it('renders a file row per entry', () => {
        const wrapper = mountFilelist(files);

        expect(rows(wrapper)).toEqual(['a.pdf', 'b.pdf']);
    });

    it('indexes each row into the field name', () => {
        const wrapper = mountFilelist(files);

        expect(names(wrapper)).toEqual(['fields[documents][0]', 'fields[documents][1]']);
    });

    it('submits an empty value when the list is empty', () => {
        const wrapper = mountFilelist([]);

        expect(rows(wrapper)).toEqual([]);
        const hidden = wrapper.find('input[type="hidden"]');
        expect(hidden.attributes('name')).toBe('fields[documents]');
        expect((hidden.element as HTMLInputElement).value).toBe('');
    });

    it('omits the empty-value input once there is a file', () => {
        const wrapper = mountFilelist(files);

        expect(wrapper.find('input[type="hidden"]').exists()).toBe(false);
    });

    it('tells each row whether it is first or last', () => {
        const wrapper = mountFilelist([{ filename: 'a.pdf' }, { filename: 'b.pdf' }, { filename: 'c.pdf' }]);

        const children = wrapper.findAllComponents(fileStub).wrappers;
        expect(children.map(c => c.props('isFirstInFilelist'))).toEqual([true, false, false]);
        expect(children.map(c => c.props('isLastInFilelist'))).toEqual([false, false, true]);
    });

    it('adds an empty file row', async () => {
        const wrapper = mountFilelist(files);

        await wrapper.find('button').trigger('click');

        expect(rows(wrapper)).toEqual(['a.pdf', 'b.pdf', '']);
        expect(names(wrapper)).toContain('fields[documents][2]');
    });

    it('stops allowing more once the limit is reached', async () => {
        const wrapper = mountFilelist(files, { limit: 2 });

        expect(wrapper.find('button').attributes('disabled')).toBeDefined();
    });

    it('never allows more when read-only', () => {
        const wrapper = mountFilelist(files, { readonly: true });

        expect(wrapper.find('button').attributes('disabled')).toBeDefined();
    });

    it('moves a file down', async () => {
        const wrapper = mountFilelist(files);

        child(wrapper, 0).moveDown();
        await nextTick();

        expect(rows(wrapper)).toEqual(['b.pdf', 'a.pdf']);
    });

    it('moves a file up', async () => {
        const wrapper = mountFilelist(files);

        child(wrapper, 1).moveUp();
        await nextTick();

        expect(rows(wrapper)).toEqual(['b.pdf', 'a.pdf']);
    });

    it('leaves the last file alone when asked to move it down', async () => {
        const wrapper = mountFilelist(files);

        child(wrapper, 1).moveDown();
        await nextTick();

        expect(rows(wrapper)).toEqual(['a.pdf', 'b.pdf']);
    });

    it('leaves the first file alone when asked to move it up', async () => {
        const wrapper = mountFilelist(files);

        child(wrapper, 0).moveUp();
        await nextTick();

        expect(rows(wrapper)).toEqual(['a.pdf', 'b.pdf']);
    });

    it('removes a file', async () => {
        const wrapper = mountFilelist(files);

        child(wrapper, 0).remove();
        await nextTick();

        expect(rows(wrapper)).toEqual(['b.pdf']);
    });

    it('addresses the right row past the tenth', async () => {
        // The row index is recovered by pulling the last run of digits out of
        // the field name, so double-digit indexes have to survive the round trip.
        const many = Array.from({ length: 12 }, (_, index) => ({ filename: `${index}.pdf` }));
        const wrapper = mountFilelist(many, { limit: 20 });

        child(wrapper, 10).remove();
        await nextTick();

        expect(rows(wrapper)).not.toContain('10.pdf');
        expect(rows(wrapper)).toHaveLength(11);
    });

    it('treats a missing file list as empty', () => {
        const wrapper = mount(Filelist, {
            propsData: { ...defaultProps, files: [] },
            stubs: { 'editor-file': fileStub },
        });

        expect(rows(wrapper)).toEqual([]);
    });
});
