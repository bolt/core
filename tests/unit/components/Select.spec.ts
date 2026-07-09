import { mount, type VueWrapper } from '@vue/test-utils';
import { defineComponent, type ComponentPublicInstance, type PropType } from 'vue';
import { describe, expect, it, beforeEach, afterEach, vi } from 'vitest';
import $ from 'jquery';
import Select from '@/editor/Components/Select.vue';
import type { SelectOption, SelectedValue } from '@/editor/types';
import Multiselect from 'vue-multiselect';

const SlotRenderingMultiselect = defineComponent({
    name: 'Multiselect',
    props: {
        modelValue: {
            type: null as unknown as PropType<SelectedValue>,
            default: null,
        },
        multiple: {
            type: Boolean,
            default: false,
        },
        options: {
            type: Array as PropType<SelectOption[]>,
            default: () => [],
        },
    },
    computed: {
        tagOptions(): SelectOption[] {
            return Array.isArray(this.modelValue) ? this.modelValue : [];
        },
    },
    methods: {
        removeElement() {},
    },
    template: `
        <div class="multiselect-slot-stub">
            <div v-if="!multiple && modelValue" class="slot-single-label">
                <slot name="singleLabel" :option="modelValue"></slot>
            </div>
            <div v-for="option in options" :key="'option-' + option.key" class="slot-option">
                <slot name="option" :option="option"></slot>
            </div>
            <div v-for="option in tagOptions" :key="'tag-' + option.key" class="slot-tag">
                <slot name="tag" :option="option"></slot>
            </div>
        </div>
    `,
});

const slotRenderingMultiselectStubs = {
    Multiselect: SlotRenderingMultiselect,
    VueMultiselect: SlotRenderingMultiselect,
    multiselect: SlotRenderingMultiselect,
};

describe('EditorSelect', () => {
    type DragDataTransfer = {
        setData?: ReturnType<typeof vi.fn>;
        getData?: (format: string) => string;
    };
    type TestDragEvent = DragEvent & {
        preventDefault: ReturnType<typeof vi.fn>;
        dataTransfer: DragDataTransfer;
        target: HTMLElement;
    };
    type SelectExpose = {
        selected: SelectedValue;
        sanitized: string;
        fieldName: string;
        hasRecordLinks: boolean;
        isLoading: boolean;
        selectOptions: SelectOption[];
        addTag: (newTag: string) => void;
        removeElement: (element: SelectOption) => void;
        findDropElement: (element: HTMLElement) => HTMLElement;
        drag: (event: DragEvent) => void;
        dragOver: (event: DragEvent) => void;
        dragLeave: (event: DragEvent) => void;
        dragEnd: (event: DragEvent) => void;
        drop: (event: DragEvent) => void;
        allowDrop: (event: DragEvent) => void;
        rawFilter: (value?: string) => string;
    };
    type SelectWrapper = VueWrapper<ComponentPublicInstance & SelectExpose>;
    type SelectWindow = Window &
        typeof globalThis & {
            $: typeof $;
            selectCache?: Record<string, SelectOption[]>;
            requestCache?: Record<string, PromiseLike<SelectOption[]>>;
        };
    let wrapper: SelectWrapper;

    function selectedItems(value: SelectedValue): SelectOption[] {
        if (!Array.isArray(value)) {
            throw new Error('Expected an array of selected options');
        }

        return value;
    }

    function createJqXHR<T>(value: T): JQuery.jqXHR<T> {
        const promise = Promise.resolve(value);
        const jqXHR: Partial<JQuery.jqXHR<T>> = {
            abort: vi.fn(),
            state: () => 'resolved',
            statusCode: () => jqXHR as JQuery.jqXHR<T>,
            always: () => jqXHR as JQuery.jqXHR<T>,
            done: () => jqXHR as JQuery.jqXHR<T>,
            fail: () => jqXHR as JQuery.jqXHR<T>,
            progress: () => jqXHR as JQuery.jqXHR<T>,
        };
        Object.defineProperty(jqXHR, 'then', {
            value: (doneFilter: (value: T) => void) => {
                promise.then(doneFilter);
                return jqXHR;
            },
        });

        return jqXHR as JQuery.jqXHR<T>;
    }

    function createDragEvent(target: HTMLElement, dataTransfer: DragDataTransfer): TestDragEvent {
        const event = new Event('drag');
        Object.defineProperties(event, {
            target: { value: target },
            dataTransfer: { value: dataTransfer },
            preventDefault: { value: vi.fn() },
        });

        return event as TestDragEvent;
    }

    const defaultProps = {
        name: 'test_select',
        id: 'test_id',
        form: 'test_form',
        options: [
            {
                key: '1',
                value: 'One',
                link_to_record_url: 'http://example.com',
            },
            { key: '2', value: 'Two' },
            { key: '3', value: 'Three' },
        ],
        value: ['1', '2'],
        multiple: true,
        taggable: true,
    };

    beforeEach(() => {
        (window as SelectWindow).$ = $;
        // Mock jQuery ajax
        vi.spyOn($, 'ajax').mockImplementation(() => {
            return createJqXHR([
                { key: 'remote1', value: 'Remote 1' },
                { key: 'remote2', value: 'Remote 2' },
            ]);
        });
        (window as SelectWindow).selectCache = {};
        (window as SelectWindow).requestCache = {};
    });

    afterEach(() => {
        if (wrapper) wrapper.unmount();
        vi.restoreAllMocks();
        delete (window as SelectWindow).selectCache;
        delete (window as SelectWindow).requestCache;
    });

    it('initializes and computes sanitized selected items', () => {
        wrapper = mount(Select, {
            props: defaultProps,
            global: { components: { multiselect: Multiselect } },
        }) as SelectWrapper;

        expect(selectedItems(wrapper.vm.selected)).toHaveLength(2);
        expect(selectedItems(wrapper.vm.selected)[0]?.key).toBe('1');

        expect(wrapper.vm.sanitized).toBe('["1","2"]');
        expect(wrapper.vm.fieldName).toBe('test_select[]');
        expect(wrapper.vm.hasRecordLinks).toBe(true);
    });

    it('renders status options through the multiselect slots', async () => {
        wrapper = mount(Select, {
            props: {
                ...defaultProps,
                name: 'status',
                value: 'published',
                multiple: false,
                taggable: false,
                options: [
                    { key: 'published', value: 'Published' },
                    { key: 'draft', value: 'Draft' },
                ],
            },
            global: {
                stubs: slotRenderingMultiselectStubs,
            },
        }) as SelectWrapper;
        await wrapper.vm.$nextTick();

        expect(wrapper.findAll('.status.is-published')).toHaveLength(2);
        expect(wrapper.find('.slot-single-label').text()).toContain('Published');
        expect(wrapper.findAll('.slot-option')[1].text()).toContain('Draft');
    });

    it('renders linked record labels and editable tags through the multiselect slots', async () => {
        wrapper = mount(Select, {
            props: {
                ...defaultProps,
                multiple: false,
                taggable: false,
                value: '1',
            },
            global: {
                stubs: slotRenderingMultiselectStubs,
            },
        }) as SelectWrapper;
        await wrapper.vm.$nextTick();

        const editLink = wrapper.find('.slot-single-label a[aria-label="Edit record"]');
        expect(editLink.attributes('href')).toBe('http://example.com');
    });

    it('renders draggable multi-select tags through the multiselect slots', async () => {
        wrapper = mount(Select, {
            props: {
                ...defaultProps,
                value: ['1', ''],
                taggable: false,
                options: [
                    defaultProps.options[0],
                    { key: '', value: '', link_to_record_url: 'http://example.com/empty' },
                ],
            },
            global: {
                stubs: slotRenderingMultiselectStubs,
            },
        }) as SelectWrapper;
        await wrapper.vm.$nextTick();

        expect(wrapper.find('.multiselect__tag__drag').exists()).toBe(true);
        expect(wrapper.find('.slot-tag .empty').exists()).toBe(true);
        expect(wrapper.find('.slot-tag a[aria-label="Edit record"]').attributes('href')).toBe('http://example.com');
        expect(wrapper.find('.multiselect__tag-icon').attributes('aria-label')).toBe('Remove element');
    });

    it('wires rendered tag drag and remove events to the select handlers', async () => {
        wrapper = mount(Select, {
            props: {
                ...defaultProps,
                taggable: false,
            },
            global: {
                stubs: slotRenderingMultiselectStubs,
            },
        }) as SelectWrapper;
        await wrapper.vm.$nextTick();

        const dataTransfer = {
            setData: vi.fn(),
            getData: () => '1',
        };
        const tag = wrapper.find('.multiselect__tag');

        await tag.trigger('dragstart', { dataTransfer });
        expect(dataTransfer.setData).toHaveBeenCalledWith('text', '1');
        expect(tag.classes()).toContain('dragging');

        await tag.trigger('dragover');
        expect(tag.classes()).not.toContain('dragover');

        await tag.trigger('dragleave');
        await tag.trigger('dragend');
        expect(tag.classes()).not.toContain('dragging');

        await wrapper.find('.slot-tag > span').trigger('dragover');
        await wrapper.find('.slot-tag > span').trigger('drop', { dataTransfer });
        await wrapper.find('.multiselect__tag-icon').trigger('mousedown');
    });

    it('sanitizes null selected items', async () => {
        wrapper = mount(Select, {
            props: { ...defaultProps, value: [] },
            global: { components: { multiselect: Multiselect } },
        }) as SelectWrapper;
        wrapper.vm.selected = null;
        expect(wrapper.vm.sanitized).toBe('[]');
    });

    it('sanitizes non-array selected items', () => {
        wrapper = mount(Select, {
            props: { ...defaultProps, value: '2', multiple: false },
            global: { components: { multiselect: Multiselect } },
        }) as SelectWrapper;

        // single-select initializes to a single object (not an array), matching
        // what vue-multiselect binds via v-model.
        expect(Array.isArray(wrapper.vm.selected)).toBe(false);
        expect((wrapper.vm.selected as SelectOption)?.key).toBe('2');
        expect(wrapper.vm.sanitized).toBe('["2"]');
    });

    it('selects an option whose key is the number 0', () => {
        // `0` is falsy, so a truthiness check on props.value would wrongly clear it.
        wrapper = mount(Select, {
            props: {
                ...defaultProps,
                options: [
                    { key: 0, value: 'Zero' },
                    { key: 1, value: 'One' },
                ],
                value: 0,
                multiple: false,
                taggable: false,
            },
            global: { components: { multiselect: Multiselect } },
        }) as SelectWrapper;

        expect(Array.isArray(wrapper.vm.selected)).toBe(false);
        expect((wrapper.vm.selected as SelectOption)?.key).toBe(0);
    });

    it('adds a tag', () => {
        wrapper = mount(Select, {
            props: defaultProps,
            global: { components: { multiselect: Multiselect } },
        }) as SelectWrapper;
        wrapper.vm.addTag('New Tag');

        expect(wrapper.vm.selectOptions.find((o: SelectOption) => o.key === 'New Tag')).toBeTruthy();
        expect(
            Array.isArray(wrapper.vm.selected)
                ? wrapper.vm.selected.find((o: SelectOption) => o.key === 'New Tag')
                : undefined,
        ).toBeTruthy();
    });

    it('replaces the selection when tagging in single-select mode', () => {
        wrapper = mount(Select, {
            props: { ...defaultProps, value: undefined, multiple: false, taggable: true },
            global: { components: { multiselect: Multiselect } },
        }) as SelectWrapper;

        wrapper.vm.addTag('First Tag');
        wrapper.vm.addTag('Second Tag');

        // Single-select must hold a single object, not accumulate an array.
        expect(Array.isArray(wrapper.vm.selected)).toBe(false);
        expect((wrapper.vm.selected as SelectOption)?.key).toBe('Second Tag');
        expect(wrapper.vm.sanitized).toBe('["Second Tag"]');
    });

    it('removes an element via refs', () => {
        // use shallowMount/mount without stubbing multiselect or just mock the ref directly
        wrapper = mount(Select, {
            props: defaultProps,
            global: { components: { multiselect: Multiselect } },
        }) as SelectWrapper;
        const vselect = wrapper.findComponent({ ref: 'vselect' });
        vselect.vm.removeElement = vi.fn();
        wrapper.vm.removeElement({ key: '1', value: 'One' });
        expect(vselect.vm.removeElement).toHaveBeenCalledWith({ key: '1', value: 'One' });
    });

    it('loads options via fetchurl', async () => {
        wrapper = mount(Select, {
            props: {
                ...defaultProps,
                options: undefined,
                fetchurl: '/api/options',
                value: 'remote1',
            },
            global: { components: { multiselect: Multiselect } },
        }) as SelectWrapper;

        expect(wrapper.vm.isLoading).toBe(true);
        // Wait for ajax promise
        await new Promise(r => setTimeout(r, 0));

        expect(wrapper.vm.isLoading).toBe(false);
        expect(wrapper.vm.selectOptions).toHaveLength(2);
        expect(selectedItems(wrapper.vm.selected)).toHaveLength(1);
        expect(selectedItems(wrapper.vm.selected)[0]?.key).toBe('remote1');
    });

    it('uses cache for fetchurl', async () => {
        (window as SelectWindow).selectCache = {
            ...(window as SelectWindow).selectCache,
            '/api/cache': [{ key: 'cached1', value: 'Cached 1' }],
        };
        wrapper = mount(Select, {
            props: {
                ...defaultProps,
                options: undefined,
                fetchurl: '/api/cache',
                value: 'cached1',
            },
            global: { components: { multiselect: Multiselect } },
        }) as SelectWrapper;

        expect(wrapper.vm.selectOptions).toHaveLength(1);
        expect(selectedItems(wrapper.vm.selected)[0]?.key).toBe('cached1');
    });

    it('uses promise cache for fetchurl', async () => {
        const p = Promise.resolve([{ key: 'prom1', value: 'Prom 1' }]);
        (window as SelectWindow).requestCache = {
            ...(window as SelectWindow).requestCache,
            '/api/prom': p,
        };
        wrapper = mount(Select, {
            props: {
                ...defaultProps,
                options: undefined,
                fetchurl: '/api/prom',
                value: 'prom1',
            },
            global: { components: { multiselect: Multiselect } },
        }) as SelectWrapper;

        await p;
        expect(wrapper.vm.selectOptions).toHaveLength(1);
    });

    it('creates select and request caches when loading remote options', async () => {
        delete (window as SelectWindow).selectCache;
        delete (window as SelectWindow).requestCache;

        wrapper = mount(Select, {
            props: {
                ...defaultProps,
                options: undefined,
                fetchurl: '/api/create-cache',
                value: 'remote2',
            },
            global: { components: { multiselect: Multiselect } },
        }) as SelectWrapper;

        await new Promise(r => setTimeout(r, 0));

        expect((window as SelectWindow).selectCache?.['/api/create-cache']).toEqual([
            { key: 'remote1', value: 'Remote 1' },
            { key: 'remote2', value: 'Remote 2' },
        ]);
        expect((window as SelectWindow).requestCache?.['/api/create-cache']).toBeDefined();
        expect(selectedItems(wrapper.vm.selected)[0]?.key).toBe('remote2');
    });

    it('handles required field with no value', () => {
        wrapper = mount(Select, {
            props: { ...defaultProps, value: undefined, required: true },
            global: { components: { multiselect: Multiselect } },
        }) as SelectWrapper;

        // Should select the first option
        expect(selectedItems(wrapper.vm.selected)).toHaveLength(1);
        expect(selectedItems(wrapper.vm.selected)[0]?.key).toBe('1');
    });

    it('leaves required selection empty when there are no options', () => {
        wrapper = mount(Select, {
            props: { ...defaultProps, options: [], value: undefined, required: true },
            global: { components: { multiselect: Multiselect } },
        }) as SelectWrapper;

        expect(selectedItems(wrapper.vm.selected)).toHaveLength(0);
        expect(wrapper.vm.sanitized).toBe('[]');
    });

    it('handles drag and drop', () => {
        wrapper = mount(Select, {
            props: defaultProps,
            global: { components: { multiselect: Multiselect } },
        }) as SelectWrapper;

        const dragTarget = document.createElement('div');
        const dragEvent = createDragEvent(dragTarget, {
            setData: vi.fn(),
            getData: () => '1',
        });
        dragEvent.target.id = '1';
        dragEvent.target.setAttribute('draggable', 'true');

        wrapper.vm.drag(dragEvent);
        expect(dragEvent.dataTransfer.setData).toHaveBeenCalledWith('text', '1');

        // drop target
        const dropTarget = document.createElement('div');
        dropTarget.id = '2'; // outgoing item
        dropTarget.setAttribute('draggable', 'true');

        const dropEvent = createDragEvent(dropTarget, { getData: () => '1' });

        wrapper.vm.dragOver(dropEvent);
        expect(dropTarget.classList.contains('dragover')).toBe(true);

        wrapper.vm.dragLeave(dropEvent);
        expect(dropTarget.classList.contains('dragover')).toBe(false);

        wrapper.vm.dragEnd(dragEvent);
        expect(dragEvent.target.classList.contains('dragging')).toBe(false);

        // Initial selected: '1', '2'
        // dragging '1' to '2', index of '1' is 0, '2' is 1
        // newPosition: 0 < 1 ? 1 + 1 : 1 = 2
        // splice(0,1) -> ['2'], splice(2,0,'1') -> ['2', '1'] (but '1' is pushed to the end)
        wrapper.vm.drop(dropEvent);

        expect(selectedItems(wrapper.vm.selected)[0]?.key).toBe('2');
        expect(selectedItems(wrapper.vm.selected)[1]?.key).toBe('1');

        wrapper.vm.allowDrop(dropEvent);
        expect(dropEvent.preventDefault).toHaveBeenCalled();
    });

    it('finds a draggable ancestor for nested drag targets', () => {
        wrapper = mount(Select, {
            props: defaultProps,
            global: { components: { multiselect: Multiselect } },
        }) as SelectWrapper;

        const parent = document.createElement('div');
        const child = document.createElement('span');
        parent.id = 'draggable-parent';
        parent.setAttribute('draggable', 'true');
        parent.appendChild(child);

        expect(wrapper.vm.findDropElement(child)).toBe(parent);
    });

    it('does not add dragover styling to the item currently being dragged', () => {
        wrapper = mount(Select, {
            props: defaultProps,
            global: { components: { multiselect: Multiselect } },
        }) as SelectWrapper;

        const target = document.createElement('div');
        target.setAttribute('draggable', 'true');
        target.classList.add('dragging');

        wrapper.vm.dragOver(createDragEvent(target, { getData: () => '1' }));

        expect(target.classList.contains('dragover')).toBe(false);
    });

    it('moves a dropped item before an earlier target', () => {
        wrapper = mount(Select, {
            props: { ...defaultProps, value: ['1', '2', '3'] },
            global: { components: { multiselect: Multiselect } },
        }) as SelectWrapper;

        const dropTarget = document.createElement('div');
        dropTarget.id = '1';
        dropTarget.setAttribute('draggable', 'true');

        wrapper.vm.drop(createDragEvent(dropTarget, { getData: () => '3' }));

        expect(selectedItems(wrapper.vm.selected).map(item => item.key)).toEqual(['3', '1', '2']);
    });

    it('ignores drops when the select is not in multiple mode', () => {
        wrapper = mount(Select, {
            props: { ...defaultProps, value: '1', multiple: false },
            global: { components: { multiselect: Multiselect } },
        }) as SelectWrapper;

        const dropTarget = document.createElement('div');
        dropTarget.id = '2';
        dropTarget.setAttribute('draggable', 'true');

        wrapper.vm.drop(createDragEvent(dropTarget, { getData: () => '1' }));

        expect((wrapper.vm.selected as SelectOption).key).toBe('1');
    });

    it('ignores drops when either selected item cannot be found', () => {
        wrapper = mount(Select, {
            props: defaultProps,
            global: { components: { multiselect: Multiselect } },
        }) as SelectWrapper;

        const dropTarget = document.createElement('div');
        dropTarget.id = 'missing';
        dropTarget.setAttribute('draggable', 'true');

        wrapper.vm.drop(createDragEvent(dropTarget, { getData: () => '1' }));

        expect(selectedItems(wrapper.vm.selected).map(item => item.key)).toEqual(['1', '2']);
    });

    it('filters raw string', () => {
        wrapper = mount(Select, {
            props: defaultProps,
            global: { components: { multiselect: Multiselect } },
        }) as SelectWrapper;
        expect(wrapper.vm.rawFilter('test')).toBe('test');
        expect(wrapper.vm.rawFilter(undefined)).toBe('');
    });
});
