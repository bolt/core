import { mount } from '@vue/test-utils';
import { describe, expect, it } from 'vitest';
import Sorting from '@/listing/Components/Table/Row/_Sorting.vue';
import { createListingStore } from '../../helpers/store';

const mountSorting = (sorting: boolean) => {
    const testStore = createListingStore({ general: { sorting } });
    return mount(Sorting, { localVue: testStore.localVue, store: testStore.store });
};

describe('Listing row Sorting handle', () => {
    it('renders the drag handle while sorting is on', () => {
        const wrapper = mountSorting(true);

        expect(wrapper.find('button.listing__row--move').exists()).toBe(true);
    });

    it('renders nothing while sorting is off', () => {
        const wrapper = mountSorting(false);

        expect(wrapper.find('button.listing__row--move').exists()).toBe(false);
    });
});
