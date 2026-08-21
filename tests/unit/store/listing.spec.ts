import { describe, expect, it } from 'vitest';
import { createListingStore } from '../helpers/store';

describe('listing store — general module', () => {
    it('starts with no type, normal rows and sorting off', () => {
        const { store } = createListingStore();

        expect(store.getters['general/getType']).toBeNull();
        expect(store.getters['general/getRowSize']).toBe('normal');
        expect(store.getters['general/getSorting']).toBe(false);
    });

    it('records the content type', async () => {
        const { store } = createListingStore();

        await store.dispatch('general/setType', 'pages');

        expect(store.getters['general/getType']).toBe('pages');
    });

    it('records the row size and the sorting flag', async () => {
        const { store } = createListingStore();

        await store.dispatch('general/setRowSize', 'compact');
        await store.dispatch('general/setSorting', true);

        expect(store.getters['general/getRowSize']).toBe('compact');
        expect(store.getters['general/getSorting']).toBe(true);
    });
});

describe('listing store — listing module', () => {
    it('starts with no records', () => {
        const { store } = createListingStore();

        expect(store.getters['listing/getRecords']).toEqual([]);
        expect(store.getters['listing/getOrder']).toEqual([]);
    });

    it('exposes the record order as a list of ids', async () => {
        const { store } = createListingStore();

        await store.dispatch('listing/setRecords', [{ id: 7 }, { id: 3 }, { id: 5 }]);

        expect(store.getters['listing/getOrder']).toEqual([7, 3, 5]);
    });

    it('reflects a reordering in the order getter', async () => {
        const { store } = createListingStore({ listing: { records: [{ id: 1 }, { id: 2 }] } });

        await store.dispatch('listing/setRecords', [{ id: 2 }, { id: 1 }]);

        expect(store.getters['listing/getOrder']).toEqual([2, 1]);
    });
});

describe('listing store — selecting module', () => {
    it('starts with nothing selected', () => {
        const { store } = createListingStore();

        expect(store.getters['selecting/selectAll']).toBe(false);
        expect(store.getters['selecting/selected']).toEqual([]);
        expect(store.getters['selecting/selectedCount']).toBe(0);
    });

    it('tracks selected ids and their count', async () => {
        const { store } = createListingStore();

        await store.dispatch('selecting/select', 1);
        await store.dispatch('selecting/select', 2);

        expect(store.getters['selecting/selected']).toEqual([1, 2]);
        expect(store.getters['selecting/selectedCount']).toBe(2);
    });

    it('removes a deselected id', async () => {
        const { store } = createListingStore();

        await store.dispatch('selecting/select', 1);
        await store.dispatch('selecting/select', 2);
        await store.dispatch('selecting/deSelect', 1);

        expect(store.getters['selecting/selected']).toEqual([2]);
        expect(store.getters['selecting/selectedCount']).toBe(1);
    });

    it('lets the count drift below the list when an unselected id is deselected', async () => {
        // Pinning current behaviour: deSelect decrements unconditionally but only
        // splices when the id is actually present, so the count and the list can
        // disagree. Components never take that path today, which is why it has
        // gone unnoticed.
        const { store } = createListingStore();

        await store.dispatch('selecting/select', 1);
        await store.dispatch('selecting/deSelect', 99);

        expect(store.getters['selecting/selected']).toEqual([1]);
        expect(store.getters['selecting/selectedCount']).toBe(0);
    });

    it('records the select-all flag', async () => {
        const { store } = createListingStore();

        await store.dispatch('selecting/selectAll', true);

        expect(store.getters['selecting/selectAll']).toBe(true);
    });
});

describe('store factory isolation', () => {
    it('gives each store its own state', async () => {
        const first = createListingStore();
        const second = createListingStore();

        await first.store.dispatch('listing/setRecords', [{ id: 1 }]);

        expect(second.store.getters['listing/getRecords']).toEqual([]);
    });
});
