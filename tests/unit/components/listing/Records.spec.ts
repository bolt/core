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
        return {
            ...testStore,
            wrapper: mount(Records, {
                localVue: testStore.localVue,
                store: testStore.store,
                propsData: { type: 'articles', data: records, labels: { title: 'Title' }, ...props },
            }),
        };
    };

    it('seeds the store from its props on creation', () => {
        const { store, dispatched } = mountRecords();

        expect(dispatched('general/setType')).toEqual(['articles']);
        expect(dispatched('listing/setRecords')).toEqual([records]);
        expect(store.getters['general/getType']).toBe('articles');
        expect(store.getters['listing/getRecords']).toEqual(records);
    });

    it('renders its container', () => {
        const { wrapper } = mountRecords();

        expect(wrapper.find('.listing-records').exists()).toBe(true);
    });
});
