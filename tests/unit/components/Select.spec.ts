import { mount } from '@vue/test-utils';
import { nextTick } from 'vue';
import { afterEach, describe, expect, it, vi } from 'vitest';
import $ from 'jquery';
import Select from '@/editor/Components/Select.vue';
import { multiselectStub } from '../helpers/stubs';
import { flushPromises } from '../helpers/async';

const options = [
    { key: 'published', value: 'Published' },
    { key: 'draft', value: 'Draft' },
    { key: 'held', value: 'Held' },
];

const defaultProps = {
    value: [],
    name: 'fields[status]',
    id: 'field-status',
    form: 'editcontent',
    options,
    optionslimit: 100,
    multiple: true,
    taggable: false,
    readonly: false,
    classname: 'is-select',
    autocomplete: false,
    errormessage: 'Pick one',
    required: false,
};

const mountSelect = (props: Record<string, unknown> = {}) =>
    mount(Select, {
        // `options` and `value` are pushed to in place by addTag, so give each
        // mount its own copy.
        propsData: { ...defaultProps, options: [...options], value: [], ...props },
        stubs: { multiselect: multiselectStub },
    });

/** The hidden input is what the form actually posts. */
const submitted = (wrapper: ReturnType<typeof mount>) =>
    (wrapper.find('input[type="hidden"]').element as HTMLInputElement).value;
const picker = (wrapper: ReturnType<typeof mount>) => wrapper.findComponent(multiselectStub);
const choose = async (wrapper: ReturnType<typeof mount>, selection: unknown) => {
    (picker(wrapper).vm as unknown as { choose: (o: unknown) => void }).choose(selection);
    await nextTick();
};

afterEach(() => {
    vi.restoreAllMocks();
});

describe('EditorSelect', () => {
    it('posts an empty list when nothing is selected', async () => {
        const wrapper = mountSelect();
        await nextTick();

        expect(submitted(wrapper)).toBe('[]');
        expect(wrapper.find('input[type="hidden"]').attributes('name')).toBe('fields[status]');
        expect(wrapper.find('input[type="hidden"]').attributes('form')).toBe('editcontent');
    });

    it('posts the keys of the initial selection', async () => {
        const wrapper = mountSelect({ value: ['draft'] });
        await nextTick();

        expect(submitted(wrapper)).toBe('["draft"]');
    });

    it('accepts a single value rather than a list', async () => {
        const wrapper = mountSelect({ value: 'held', multiple: false });
        await nextTick();

        expect(submitted(wrapper)).toBe('["held"]');
    });

    it('ignores an initial value that is not among the options', async () => {
        const wrapper = mountSelect({ value: ['nonexistent'] });
        await nextTick();

        expect(submitted(wrapper)).toBe('[]');
    });

    it('falls back to the first option when a required field has no valid value', async () => {
        const wrapper = mountSelect({ value: [], required: 'required' });
        await nextTick();

        expect(submitted(wrapper)).toBe('["published"]');
    });

    it('leaves an optional field empty', async () => {
        const wrapper = mountSelect({ value: [], required: false });
        await nextTick();

        expect(submitted(wrapper)).toBe('[]');
    });

    it('posts what the user picks', async () => {
        const wrapper = mountSelect();
        await nextTick();

        await choose(wrapper, [options[1], options[2]]);

        expect(submitted(wrapper)).toBe('["draft","held"]');
    });

    it('posts a single pick that is not in a list', async () => {
        const wrapper = mountSelect({ multiple: false });
        await nextTick();

        await choose(wrapper, options[0]);

        expect(submitted(wrapper)).toBe('["published"]');
    });

    it('posts an empty list when the selection is cleared', async () => {
        const wrapper = mountSelect({ value: ['draft'] });
        await nextTick();

        await choose(wrapper, null);

        expect(submitted(wrapper)).toBe('[]');
    });

    it('hands the multiselect the options and the multiple flag', async () => {
        const wrapper = mountSelect();
        await nextTick();

        expect(picker(wrapper).props('options')).toEqual(options);
        expect(picker(wrapper).props('multiple')).toBe(true);
    });

    it('disables the picker when read-only', async () => {
        const wrapper = mountSelect({ readonly: true });
        await nextTick();

        expect(picker(wrapper).props('disabled')).toBe(true);
    });

    it('applies the field class name to the wrapper', async () => {
        const wrapper = mountSelect();
        await nextTick();

        expect(wrapper.classes()).toContain('is-select');
        expect(wrapper.attributes('id')).toBe('multiselect-field-status');
    });

    it('adds a new tag to the options and the selection', async () => {
        const wrapper = mountSelect({ taggable: true });
        await nextTick();

        (picker(wrapper).vm as unknown as { tag: (label: string) => void }).tag('Brand new');
        await nextTick();

        expect(submitted(wrapper)).toBe('["Brand new"]');
        expect((picker(wrapper).props('options') as { key: string }[]).map(o => o.key)).toContain('Brand new');
    });

    it('renders a status marker for each option of the status field', async () => {
        const wrapper = mountSelect({ name: 'status' });
        await nextTick();

        const rendered = wrapper.findAll('.stub-option').wrappers;
        expect(rendered).toHaveLength(3);
        expect(rendered[0].find('.status').classes()).toContain('is-published');
        expect(rendered[0].text()).toBe('Published');
    });

    it('renders a removable tag per selected item for other fields', async () => {
        const wrapper = mountSelect({ value: ['draft', 'held'] });
        await nextTick();

        const tags = wrapper.findAll('.stub-tag .multiselect__tag').wrappers;
        expect(tags).toHaveLength(2);
        expect(tags[0].attributes('id')).toBe('draft');
        expect(tags[0].attributes('draggable')).toBe('true');
        expect(tags[0].find('.multiselect__tag-icon').exists()).toBe(true);
    });

    it('offers an edit link on a tag that has one', async () => {
        const linked = [{ key: 'a', value: 'A record', link_to_record_url: '/bolt/edit/1' }];
        const wrapper = mountSelect({ options: linked, value: ['a'] });
        await nextTick();

        expect(wrapper.find('.multiselect__tag__edit a').attributes('href')).toBe('/bolt/edit/1');
    });

    it('does not offer dragging when tags are freely editable', async () => {
        const wrapper = mountSelect({ value: ['draft'], taggable: true });
        await nextTick();

        expect(wrapper.find('.stub-tag .multiselect__tag').attributes('draggable')).toBe('false');
        expect(wrapper.find('.multiselect__tag__drag').exists()).toBe(false);
    });

    it('reorders the selection when one tag is dropped onto another', async () => {
        const wrapper = mountSelect({ value: ['published', 'draft', 'held'] });
        await nextTick();
        expect(submitted(wrapper)).toBe('["published","draft","held"]');

        const tags = wrapper.findAll('.stub-tag .multiselect__tag').wrappers;
        await tags[0].trigger('dragstart', { dataTransfer: { setData: vi.fn() } });
        await tags[2].trigger('drop', { dataTransfer: { getData: () => 'published' } });

        expect(submitted(wrapper)).toBe('["draft","held","published"]');
    });

    it('marks the tag being dragged, and clears it again', async () => {
        const wrapper = mountSelect({ value: ['published', 'draft'] });
        await nextTick();

        const tag = wrapper.findAll('.stub-tag .multiselect__tag').at(0);
        await tag.trigger('dragstart', { dataTransfer: { setData: vi.fn() } });
        expect(tag.classes()).toContain('dragging');

        await tag.trigger('dragend');
        expect(tag.classes()).not.toContain('dragging');
    });

    it('highlights the tag being dragged over, but not the dragged one', async () => {
        const wrapper = mountSelect({ value: ['published', 'draft'] });
        await nextTick();

        const [dragged, target] = wrapper.findAll('.stub-tag .multiselect__tag').wrappers;
        await dragged.trigger('dragstart', { dataTransfer: { setData: vi.fn() } });

        await target.trigger('dragover');
        expect(target.classes()).toContain('dragover');

        await dragged.trigger('dragover');
        expect(dragged.classes()).not.toContain('dragover');

        await target.trigger('dragleave');
        expect(target.classes()).not.toContain('dragover');
    });

    describe('with a fetchurl', () => {
        const fetched = [{ key: 'remote', value: 'Remote option' }];
        const fetchurl = '/bolt/async/options';

        const stubAjax = (response: unknown[]) => {
            const ajax = vi
                .spyOn($, 'ajax')
                .mockReturnValue(Promise.resolve(response) as unknown as ReturnType<typeof $.ajax>);
            return ajax;
        };

        it('loads its options from the server', async () => {
            const ajax = stubAjax(fetched);
            const wrapper = mountSelect({ fetchurl, options: [], value: ['remote'] });
            await flushPromises();
            await nextTick();

            expect(ajax).toHaveBeenCalledWith({ url: fetchurl, dataType: 'json', cache: true });
            expect(picker(wrapper).props('options')).toEqual(fetched);
            expect(submitted(wrapper)).toBe('["remote"]');
        });

        it('serves a second field from the response cache without asking again', async () => {
            const ajax = stubAjax(fetched);
            mountSelect({ fetchurl, options: [], value: [] });
            await flushPromises();
            expect(ajax).toHaveBeenCalledTimes(1);

            const second = mountSelect({ fetchurl, options: [], value: ['remote'] });
            await nextTick();

            expect(ajax).toHaveBeenCalledTimes(1);
            expect(second.findComponent(multiselectStub).props('options')).toEqual(fetched);
        });

        it('shares one in-flight request between fields mounted together', async () => {
            const ajax = stubAjax(fetched);
            const first = mountSelect({ fetchurl, options: [], value: ['remote'] });
            const second = mountSelect({ fetchurl, options: [], value: ['remote'] });
            await flushPromises();
            await nextTick();

            expect(ajax).toHaveBeenCalledTimes(1);
            expect(first.findComponent(multiselectStub).props('options')).toEqual(fetched);
            expect(second.findComponent(multiselectStub).props('options')).toEqual(fetched);
        });
    });

    it.skip('keeps an option whose key is the number 0 selected', () => {
        // Only passes after 8a8b199e. `fixSelectedItems` starts with `!this.value
        // ? [] : ...`, so a value of 0 is treated as no value at all and the
        // selection is silently dropped.
    });

    it.skip('replaces the selection when tagging in single-select mode', () => {
        // Only passes after 8d54e750. addTag pushes unconditionally, so tagging
        // a single-select field ends up with two selected items.
    });
});
