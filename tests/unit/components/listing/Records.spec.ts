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
                localVue: testStore.localVue,
                pinia: testStore.pinia,
                propsData: { type: 'articles', data: records, labels: { title: 'Title' }, ...props },
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

    it('renders its container', () => {
        const { wrapper } = mountRecords();

        expect(wrapper.find('.listing-records').exists()).toBe(true);
    });
});
