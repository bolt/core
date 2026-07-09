import { mount } from '@vue/test-utils';
import Records from '@/listing/Components/Records.vue';
import { describe, it, expect, beforeEach } from 'vitest';
import { createPinia, setActivePinia } from 'pinia';
import { useGeneralStore, useListingStore } from '@/listing/store';

describe('Listing Records Component', () => {
    beforeEach(() => {
        setActivePinia(createPinia());
    });

    it('renders correctly and sets initial store state', () => {
        const type = 'articles';
        const data = [
            { id: 1, title: 'Test 1' },
            { id: 2, title: 'Test 2' },
        ];
        const labels = { title: 'Title' };

        const wrapper = mount(Records, {
            props: { type, data, labels },
        });

        expect(wrapper.find('.listing-records').exists()).toBe(true);

        const generalStore = useGeneralStore();
        const listingStore = useListingStore();

        expect(generalStore.type).toBe(type);
        expect(listingStore.records).toEqual(data);
    });
});
