import { mount } from '@vue/test-utils';
import { nextTick } from 'vue';
import { describe, expect, it } from 'vitest';
import Filter from '@/listing/Components/Filter.vue';
import { createListingStore, type ListingSeed } from '../../helpers/store';

const labels = {
    select_all: 'Select all',
    button_expanded: 'Expanded view',
    button_compact: 'Compact view',
};

const mountFilter = (seed: ListingSeed = {}) => {
    const testStore = createListingStore(seed);
    const selectAlls = testStore.writes('selecting', 'selectAll');
    const rowSizes = testStore.writes('general', 'rowSize');
    return {
        ...testStore,
        selectAlls,
        rowSizes,
        wrapper: mount(Filter, { localVue: testStore.localVue, pinia: testStore.pinia, propsData: { labels } }),
    };
};

describe('Listing Filter', () => {
    it('offers select-all on a content type listing', () => {
        const { wrapper } = mountFilter({ general: { type: 'pages' } });

        expect(wrapper.find('#selectAll').exists()).toBe(true);
        expect(wrapper.find('.control--checkbox .sr-only').text()).toBe('Select all');
    });

    it('hides select-all on the dashboard', () => {
        const { wrapper } = mountFilter({ general: { type: 'dashboard' } });

        expect(wrapper.find('.control--checkbox').exists()).toBe(false);
    });

    it('hides select-all while sorting is on', () => {
        const { wrapper } = mountFilter({ general: { type: 'pages', sorting: true } });

        expect(wrapper.find('#selectAll').exists()).toBe(false);
    });

    it('turns select-all on and off again', async () => {
        const { wrapper, stores, selectAlls } = mountFilter({ general: { type: 'pages' } });

        await wrapper.find('#selectAll').trigger('click');
        expect(stores.selecting.selectAll).toBe(true);

        await wrapper.find('#selectAll').trigger('click');
        expect(stores.selecting.selectAll).toBe(false);
        expect(selectAlls).toEqual([true, false]);
    });

    it('marks the button matching the current row size', () => {
        const { wrapper } = mountFilter({ general: { type: 'pages', rowSize: 'small' } });

        const [expanded, compact] = wrapper.findAll('.control--button').wrappers;
        expect(expanded.classes()).not.toContain('is-selected');
        expect(compact.classes()).toContain('is-selected');
    });

    it('switches the row size and remembers it', async () => {
        const { wrapper, stores, rowSizes } = mountFilter({ general: { type: 'pages' } });

        await wrapper.findAll('.control--button').at(1).trigger('click');

        expect(stores.general.rowSize).toBe('small');
        expect(localStorage.getItem('listing-row-size')).toBe('small');
        expect(rowSizes).toEqual(['small']);
    });

    it('restores a remembered row size on creation', () => {
        localStorage.setItem('listing-row-size', 'small');

        const { stores, rowSizes } = mountFilter({ general: { type: 'pages' } });

        expect(stores.general.rowSize).toBe('small');
        expect(rowSizes).toEqual(['small']);
    });

    it('keeps the default row size when nothing is remembered', () => {
        const { stores, rowSizes } = mountFilter({ general: { type: 'pages' } });

        expect(stores.general.rowSize).toBe('normal');
        expect(rowSizes).toEqual([]);
    });

    it('can turn sorting on, though no control is rendered for it', async () => {
        // `enableSorting` is dead code: the only button that called it is
        // commented out in the template. Pinned through the instance so the
        // Vue 3 migration can decide whether the feature comes back or goes.
        const { wrapper, stores } = mountFilter({ general: { type: 'pages' } });

        await (wrapper.vm as unknown as { enableSorting: (on: boolean) => void }).enableSorting(true);

        expect(stores.general.sorting).toBe(true);
        expect(wrapper.find('.control--button.is-active').exists()).toBe(false);
    });

    it('clears select-all when sorting starts', async () => {
        const { stores, selectAlls } = mountFilter({ general: { type: 'pages' } });

        stores.selecting.selectAll = true;
        stores.general.sorting = true;
        await nextTick();

        expect(stores.selecting.selectAll).toBe(false);
        expect(selectAlls).toEqual([true, false]);
    });
});
