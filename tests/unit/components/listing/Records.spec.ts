import { mount } from '@vue/test-utils';
import { describe, expect, it } from 'vitest';
import Records from '@/listing/Components/Records.vue';
import { createListingStore } from '../../helpers/store';

describe('Listing Records', () => {
    const records = [
        { id: 1, title: 'Test 1' },
        { id: 2, title: 'Test 2' },
    ];

    const mountRecords = (props: Record<string, unknown> = {}) => {
        const testStore = createListingStore();
        const types = testStore.writes('general', 'type');
        const written = testStore.writes('listing', 'records');
        return {
            ...testStore,
            types,
            written,
            wrapper: mount(Records, {
                global: { plugins: [testStore.pinia] },
                props: { type: 'articles', data: records, labels: { title: 'Title' }, ...props },
            }),
        };
    };

    it('seeds the store from its props on creation', () => {
        const { stores, types, written } = mountRecords();

        expect(types).toEqual(['articles']);
        expect(written).toEqual([records]);
        expect(stores.general.type).toBe('articles');
        expect(stores.listing.records).toEqual(records);
    });

    it('leaves the store alone for props it was not given', () => {
        const { stores, types, written } = mountRecords({ type: undefined, data: undefined });

        expect(types).toEqual([]);
        expect(written).toEqual([]);
        expect(stores.general.type).toBeNull();
    });

    it('renders its container', () => {
        const { wrapper } = mountRecords();

        expect(wrapper.find('.listing-records').exists()).toBe(true);
    });
});
