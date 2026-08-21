import { mount } from '@vue/test-utils';
import { nextTick } from 'vue';
import { describe, expect, it } from 'vitest';
import Table from '@/listing/Components/Table/index.vue';
import { createListingStore } from '../../helpers/store';
import { draggableStub } from '../../helpers/stubs';

const labels = { actions: {} };

const records = [
    { id: 1, title: 'First' },
    { id: 2, title: 'Second' },
];

// An explicit stub rather than `'table-row': true`, so the assertions below key
// off a class we chose instead of test-utils' auto-stub naming convention.
const rowStub = {
    name: 'TableRow',
    props: {
        record: { type: Object, required: true },
        labels: { type: Object, default: () => ({}) },
    },
    template: '<div class="row-stub">{{ record.id }}</div>',
};

const renderedIds = (wrapper: ReturnType<typeof mount>) =>
    wrapper.findAll('.row-stub').wrappers.map(row => Number(row.text()));

const mountTable = (seedRecords: unknown[]) => {
    const testStore = createListingStore({ listing: { records: seedRecords } });
    return {
        ...testStore,
        wrapper: mount(Table, {
            localVue: testStore.localVue,
            store: testStore.store,
            propsData: { labels },
            stubs: { draggable: draggableStub, 'table-row': rowStub },
        }),
    };
};

describe('Listing Table', () => {
    it('renders the container but no rows when the store is empty', () => {
        const { wrapper } = mountTable([]);

        expect(wrapper.find('.listing__records').exists()).toBe(true);
        expect(renderedIds(wrapper)).toEqual([]);
    });

    it('renders a row per record in the store', () => {
        const { wrapper } = mountTable(records);

        expect(renderedIds(wrapper)).toEqual([1, 2]);
    });

    it('hands the records to the draggable list', () => {
        const { wrapper } = mountTable(records);

        expect(wrapper.findComponent(draggableStub).props('value')).toEqual(records);
    });

    it('writes a reordering back to the store', async () => {
        const { wrapper, store, dispatched } = mountTable(records);
        const reordered = [records[1], records[0]];

        (wrapper.findComponent(draggableStub).vm as unknown as { reorder: (list: unknown[]) => void }).reorder(
            reordered,
        );
        await nextTick();

        expect(dispatched('listing/setRecords')).toEqual([reordered]);
        expect(store.getters['listing/getOrder']).toEqual([2, 1]);
    });

    it('re-renders in the new order after a reordering', async () => {
        const { wrapper, store } = mountTable(records);

        await store.dispatch('listing/setRecords', [records[1], records[0]]);
        await nextTick();

        expect(renderedIds(wrapper)).toEqual([2, 1]);
    });

    it('passes the labels down to every row', () => {
        const { wrapper } = mountTable(records);

        const rows = wrapper.findAllComponents(rowStub).wrappers;
        expect(rows).toHaveLength(2);
        rows.forEach(row => expect(row.props('labels')).toBe(labels));
    });
});
