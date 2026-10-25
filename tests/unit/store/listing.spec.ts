import { describe, expect, it } from 'vitest';
import { createListingStore } from '../helpers/store';

describe('listing store — general', () => {
    it('starts with no type, normal rows and sorting off', () => {
        const { stores } = createListingStore();

        expect(stores.general.type).toBeNull();
        expect(stores.general.rowSize).toBe('normal');
        expect(stores.general.sorting).toBe(false);
    });

    it('can be seeded', () => {
        const { stores } = createListingStore({ general: { type: 'pages', rowSize: 'compact', sorting: true } });

        expect(stores.general.type).toBe('pages');
        expect(stores.general.rowSize).toBe('compact');
        expect(stores.general.sorting).toBe(true);
    });
});

describe('listing store — listing', () => {
    it('starts with no records', () => {
        const { stores } = createListingStore();

        expect(stores.listing.records).toEqual([]);
    });
});

describe('listing store — selecting', () => {
    it('starts with nothing selected', () => {
        const { stores } = createListingStore();

        expect(stores.selecting.selectAll).toBe(false);
        expect(stores.selecting.selected).toEqual([]);
        expect(stores.selecting.selectedCount).toBe(0);
    });

    it('tracks selected ids and their count', () => {
        const { stores } = createListingStore();

        stores.selecting.select(1);
        stores.selecting.select(2);

        expect(stores.selecting.selected).toEqual([1, 2]);
        expect(stores.selecting.selectedCount).toBe(2);
    });

    it('removes a deselected id', () => {
        const { stores } = createListingStore();

        stores.selecting.select(1);
        stores.selecting.select(2);
        stores.selecting.deSelect(1);

        expect(stores.selecting.selected).toEqual([2]);
        expect(stores.selecting.selectedCount).toBe(1);
    });

    it('lets the count drift below the list when an unselected id is deselected', () => {
        // Pinning current behaviour: deSelect decrements unconditionally but only
        // splices when the id is actually present, so the count and the list can
        // disagree. Components never take that path today, which is why it has
        // gone unnoticed.
        const { stores } = createListingStore();

        stores.selecting.select(1);
        stores.selecting.deSelect(99);

        expect(stores.selecting.selected).toEqual([1]);
        expect(stores.selecting.selectedCount).toBe(0);
    });
});

describe('store factory isolation', () => {
    it('gives each store its own state', () => {
        const first = createListingStore();
        const second = createListingStore();

        first.stores.listing.records = [{ id: 1 }];

        expect(second.stores.listing.records).toEqual([]);
    });
});
