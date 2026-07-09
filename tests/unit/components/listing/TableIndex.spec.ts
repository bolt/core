import type { Pinia } from 'pinia';
import { mount, type VueWrapper } from '@vue/test-utils';
import { defineComponent, type ComponentPublicInstance, type PropType } from 'vue';
import TableIndex from '@/listing/Components/Table/index.vue';
import { describe, it, expect, beforeEach } from 'vitest';
import { createPinia, setActivePinia } from 'pinia';
import { useListingStore } from '@/listing/store';
import type { ListingRecord } from '@/listing/types';

const DraggableSlotStub = defineComponent({
    name: 'Draggable',
    props: {
        modelValue: {
            type: Array as PropType<ListingRecord[]>,
            default: () => [],
        },
    },
    emits: ['update:modelValue'],
    template: `
        <div class="draggable-slot-stub">
            <button class="reverse-records" @click="$emit('update:modelValue', [...modelValue].reverse())"></button>
            <slot v-for="element in modelValue" name="item" :element="element"></slot>
        </div>
    `,
});

const draggableStubs = {
    Draggable: DraggableSlotStub,
    draggable: DraggableSlotStub,
};

describe('Listing Table Component', () => {
    type TableExpose = {
        records?: { id: number }[];
    };
    let listingStore: ReturnType<typeof useListingStore>;
    let pinia: Pinia;

    beforeEach(() => {
        pinia = createPinia();
        setActivePinia(pinia);
        listingStore = useListingStore();
    });

    const defaultProps = {
        labels: {
            edit: 'Edit',
        },
    };

    const mountComponent = (): VueWrapper<ComponentPublicInstance & TableExpose> => {
        return mount(TableIndex, {
            props: defaultProps,
            global: {
                plugins: [pinia],
                stubs: {
                    ...draggableStubs,
                    'table-row': true,
                },
            },
        }) as VueWrapper<ComponentPublicInstance & TableExpose>;
    };

    it('renders correctly with empty records', () => {
        const wrapper = mountComponent();
        expect(wrapper.find('.listing__records').exists()).toBe(true);
        expect(wrapper.findAllComponents({ name: 'table-row' })).toHaveLength(0);
    });

    it('renders correctly with records', async () => {
        listingStore.records = [{ id: 1 }, { id: 2 }];
        const wrapper = mountComponent();
        await wrapper.vm.$nextTick();

        expect(wrapper.find('.draggable-slot-stub').exists()).toBe(true);
        expect(wrapper.findAllComponents({ name: 'table-row' })).toHaveLength(2);
    });

    it('updates the listing store when draggable changes the record order', async () => {
        listingStore.records = [{ id: 1 }, { id: 2 }];
        const wrapper = mountComponent();

        await wrapper.find('.reverse-records').trigger('click');

        expect(listingStore.records).toEqual([{ id: 2 }, { id: 1 }]);
    });

    it('gets records from store', () => {
        listingStore.records = [{ id: 1 }, { id: 2 }];
        const wrapper = mountComponent();
        const component = wrapper.vm;

        expect(component.records).toEqual([{ id: 1 }, { id: 2 }]);
    });

    it('sets records to store', () => {
        const wrapper = mountComponent();
        const component = wrapper.vm;

        component.records = [{ id: 3 }];

        expect(listingStore.records).toEqual([{ id: 3 }]);
    });
});
