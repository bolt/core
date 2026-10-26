import { mount } from '@vue/test-utils';
import { nextTick } from 'vue';
import { describe, expect, it } from 'vitest';
import Checkbox from '@/listing/Components/Table/Row/_Checkbox.vue';
import { createListingStore, type ListingSeed } from '../../helpers/store';

const mountCheckbox = (seed: ListingSeed = {}) => {
    const testStore = createListingStore(seed);
    return {
        ...testStore,
        selects: testStore.calls('selecting', 'select'),
        deSelects: testStore.calls('selecting', 'deSelect'),
        wrapper: mount(Checkbox, { localVue: testStore.localVue, pinia: testStore.pinia, propsData: { id: 7 } }),
    };
};

describe('Listing row Checkbox', () => {
    it('renders a checkbox labelled by the record id', () => {
        const { wrapper } = mountCheckbox();

        expect(wrapper.find('input[type="checkbox"]').attributes('id')).toBe('row-7');
        expect(wrapper.find('label').attributes('for')).toBe('row-7');
    });

    it('is hidden while sorting is on', async () => {
        const { wrapper, stores } = mountCheckbox();
        expect(wrapper.find('.listing--checkbox').isVisible()).toBe(true);

        stores.general.sorting = true;
        await nextTick();

        expect(wrapper.find('.listing--checkbox').isVisible()).toBe(false);
    });

    it('selects the record in the store when ticked', async () => {
        const { wrapper, stores, selects } = mountCheckbox();
        await wrapper.find('input').setChecked(true);

        expect(selects).toEqual([[7]]);
        expect(stores.selecting.selected).toEqual([7]);
    });

    it('deselects the record when unticked', async () => {
        const { wrapper, stores, deSelects } = mountCheckbox();
        await wrapper.find('input').setChecked(true);
        await wrapper.find('input').setChecked(false);

        expect(deSelects).toEqual([[7]]);
        expect(stores.selecting.selected).toEqual([]);
    });

    it('follows the select-all flag', async () => {
        const { wrapper, stores, selects, deSelects } = mountCheckbox();

        stores.selecting.selectAll = true;
        await nextTick();
        expect((wrapper.find('input').element as HTMLInputElement).checked).toBe(true);
        expect(selects).toEqual([[7]]);

        stores.selecting.selectAll = false;
        await nextTick();
        expect((wrapper.find('input').element as HTMLInputElement).checked).toBe(false);
        expect(deSelects).toEqual([[7]]);
    });

    it('keeps its selection when sorting is turned off again', async () => {
        const { wrapper, stores } = mountCheckbox({ general: { sorting: true } });
        await wrapper.find('input').setChecked(true);

        stores.general.sorting = false;
        await nextTick();

        expect((wrapper.find('input').element as HTMLInputElement).checked).toBe(true);
    });

    it('clears its selection when sorting starts', async () => {
        const { wrapper, stores, deSelects } = mountCheckbox();
        await wrapper.find('input').setChecked(true);

        stores.general.sorting = true;
        await nextTick();

        expect((wrapper.find('input').element as HTMLInputElement).checked).toBe(false);
        expect(deSelects).toEqual([[7]]);
    });
});
