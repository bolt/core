import type { Pinia } from 'pinia';
import { mount, type VueWrapper } from '@vue/test-utils';
import { defineComponent, type ComponentPublicInstance, type PropType } from 'vue';
import SelectBox from '@/listing/Components/SelectBox.vue';
import { describe, it, expect, beforeEach } from 'vitest';
import { createPinia, setActivePinia } from 'pinia';
import { useSelectingStore } from '@/listing/store';
import type { BulkAction } from '@/listing/types';

const SlotRenderingMultiselect = defineComponent({
    name: 'VueMultiselect',
    props: {
        options: {
            type: Array as PropType<BulkAction[]>,
            default: () => [],
        },
    },
    emits: ['update:modelValue'],
    template: `
        <div class="multiselect-slot-stub">
            <button class="select-first-bulk-option" @click="$emit('update:modelValue', options[0])"></button>
            <div v-for="option in options" :key="option.key" class="bulk-option">
                <slot name="option" :option="option"></slot>
            </div>
        </div>
    `,
});

const multiselectStubs = {
    Multiselect: SlotRenderingMultiselect,
    VueMultiselect: SlotRenderingMultiselect,
    multiselect: SlotRenderingMultiselect,
};

describe('Listing SelectBox Component', () => {
    type SelectBoxExpose = {
        selectedAction?: { key: string } | null;
    };
    let selectingStore: ReturnType<typeof useSelectingStore>;
    let pinia: Pinia;

    beforeEach(() => {
        pinia = createPinia();
        setActivePinia(pinia);
        selectingStore = useSelectingStore();
    });

    const defaultProps = {
        singular: 'record',
        plural: 'records',
        labels: {
            selected: 'selected',
            update_all: 'Update All',
            status_to_published: 'Publish',
            status_to_draft: 'Draft',
            status_to_held: 'Hold',
            delete: 'Delete',
        },
        csrftoken: 'fake-token',
        backendPrefix: '/bolt/',
    };

    const mountComponent = (): VueWrapper<ComponentPublicInstance & SelectBoxExpose> => {
        return mount(SelectBox, {
            props: defaultProps,
            global: {
                plugins: [pinia],
                stubs: multiselectStubs,
            },
        }) as VueWrapper<ComponentPublicInstance & SelectBoxExpose>;
    };

    it('does not render when selectedCount is 0', () => {
        const wrapper = mountComponent();
        expect(wrapper.find('.card').exists()).toBe(false);
    });

    it('renders singular text when selectedCount is 1', async () => {
        const wrapper = mountComponent();
        selectingStore.select(1);
        await wrapper.vm.$nextTick();

        expect(wrapper.find('.card').exists()).toBe(true);
        expect(wrapper.find('.card-header').text()).toContain('1record selected');
    });

    it('renders plural text when selectedCount is > 1', async () => {
        const wrapper = mountComponent();
        selectingStore.select(1);
        selectingStore.select(2);
        await wrapper.vm.$nextTick();

        expect(wrapper.find('.card-header').text()).toContain('2records selected');
    });

    it('derives selectedCount from unique selected records', async () => {
        const wrapper = mountComponent();
        selectingStore.select(1);
        selectingStore.select(1);
        selectingStore.select(2);
        selectingStore.deSelect(999);
        await wrapper.vm.$nextTick();

        expect(selectingStore.selected).toEqual([1, 2]);
        expect(selectingStore.selectedCount).toBe(2);
        expect(wrapper.find('.card-header').text()).toContain('2records selected');
    });

    it('disables submit button when no action is selected', async () => {
        selectingStore.select(1);
        const wrapper = mountComponent();
        await wrapper.vm.$nextTick();

        const button = wrapper.find('button[type="submit"]');
        expect(button.attributes('disabled')).toBeDefined();
    });

    it('sets the correct postUrl when an action is selected', async () => {
        selectingStore.select(1);
        const wrapper = mountComponent();
        await wrapper.vm.$nextTick();

        const component = wrapper.vm;
        component.selectedAction = { key: 'status/published' };
        await wrapper.vm.$nextTick();

        const form = wrapper.find('form');
        expect(form.attributes('action')).toBe('/bolt/bulk/status/published');

        const button = wrapper.find('button[type="submit"]');
        expect(button.attributes('disabled')).toBeUndefined();
    });

    it('uses a relative bulk action URL when no backend prefix is configured', async () => {
        selectingStore.select(1);
        const wrapper = mount(SelectBox, {
            props: {
                ...defaultProps,
                backendPrefix: undefined,
            },
            global: {
                plugins: [pinia],
                stubs: multiselectStubs,
            },
        }) as VueWrapper<ComponentPublicInstance & SelectBoxExpose>;
        await wrapper.vm.$nextTick();

        wrapper.vm.selectedAction = { key: 'delete' };
        await wrapper.vm.$nextTick();

        expect(wrapper.find('form').attributes('action')).toBe('bulk/delete');
    });

    it('renders all bulk action options with their icon classes', async () => {
        selectingStore.select(1);
        const wrapper = mountComponent();
        await wrapper.vm.$nextTick();

        const options = wrapper.findAll('.bulk-option');
        expect(options).toHaveLength(4);
        expect(options[0].find('.status.is-published').exists()).toBe(true);
        expect(options[3].find('.fa-trash').exists()).toBe(true);
        expect(options[3].text()).toContain('Delete');
    });

    it('updates the selected bulk action from the multiselect v-model', async () => {
        selectingStore.select(1);
        const wrapper = mountComponent();
        await wrapper.vm.$nextTick();

        await wrapper.find('.select-first-bulk-option').trigger('click');
        await wrapper.vm.$nextTick();

        expect(wrapper.find('form').attributes('action')).toBe('/bolt/bulk/status/published');
        expect(wrapper.find('button[type="submit"]').attributes('disabled')).toBeUndefined();
    });

    it('renders hidden inputs correctly', async () => {
        selectingStore.select(10);
        selectingStore.select(20);
        const wrapper = mountComponent();
        await wrapper.vm.$nextTick();

        const csrfInput = wrapper.find('input[name="_csrf_token"]');
        expect(csrfInput.attributes('value')).toBe('fake-token');

        const recordsInput = wrapper.find('input[name="records"]');
        expect(recordsInput.attributes('value')).toBe('10,20');
    });
});
