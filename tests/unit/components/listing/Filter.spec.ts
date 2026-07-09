import type { Pinia } from 'pinia';
import { mount } from '@vue/test-utils';
import Filter from '@/listing/Components/Filter.vue';
import { describe, it, expect, beforeEach } from 'vitest';
import { createPinia, setActivePinia } from 'pinia';
import { useGeneralStore, useSelectingStore } from '@/listing/store';

describe('Listing Filter Component', () => {
    let generalStore: ReturnType<typeof useGeneralStore>;
    let selectingStore: ReturnType<typeof useSelectingStore>;
    let pinia: Pinia;

    beforeEach(() => {
        pinia = createPinia();
        setActivePinia(pinia);
        generalStore = useGeneralStore();
        selectingStore = useSelectingStore();
        localStorage.clear();
    });

    const defaultProps = {
        labels: {
            select_all: 'Select All',
            button_expanded: 'Expanded',
            button_compact: 'Compact',
        },
    };

    const mountComponent = () => {
        return mount(Filter, {
            props: defaultProps,
            global: {
                plugins: [pinia],
            },
        });
    };

    it('renders and shows select all when not dashboard and not sorting', () => {
        generalStore.type = 'articles';
        generalStore.sorting = false;
        const wrapper = mountComponent();

        const selectAllInput = wrapper.find('input#selectAll');
        expect(selectAllInput.exists()).toBe(true);
    });

    it('hides select all when type is dashboard', () => {
        generalStore.type = 'dashboard';
        const wrapper = mountComponent();

        expect(wrapper.find('input#selectAll').exists()).toBe(false);
    });

    it('hides select all when sorting is true', () => {
        generalStore.type = 'articles';
        generalStore.sorting = true;
        const wrapper = mountComponent();

        expect(wrapper.find('input#selectAll').exists()).toBe(false);
    });

    it('toggles selectAll state when checkbox is clicked', async () => {
        generalStore.type = 'articles';
        const wrapper = mountComponent();

        const selectAllInput = wrapper.find('input#selectAll');
        await selectAllInput.trigger('click');

        expect(selectingStore.selectAll).toBe(true);

        await selectAllInput.trigger('click');
        expect(selectingStore.selectAll).toBe(false);
    });

    it('changes row size to normal when expanded button is clicked', async () => {
        generalStore.rowSize = 'small';
        const wrapper = mountComponent();

        const buttons = wrapper.findAll('.control--button');
        const expandedButton = buttons[0];

        await expandedButton.trigger('click');

        expect(generalStore.rowSize).toBe('normal');
        expect(localStorage.getItem('listing-row-size')).toBe('normal');
    });

    it('changes row size to small when compact button is clicked', async () => {
        generalStore.rowSize = 'normal';
        const wrapper = mountComponent();

        const buttons = wrapper.findAll('.control--button');
        const compactButton = buttons[1];

        await compactButton.trigger('click');

        expect(generalStore.rowSize).toBe('small');
        expect(localStorage.getItem('listing-row-size')).toBe('small');
    });

    it('reads row size from localStorage on create', () => {
        localStorage.setItem('listing-row-size', 'small');
        mountComponent();

        expect(generalStore.rowSize).toBe('small');
    });

    it('unsets selectAll when sorting becomes true', async () => {
        generalStore.type = 'articles';
        const wrapper = mountComponent();

        selectingStore.selectAll = true;
        expect(selectingStore.selectAll).toBe(true);

        generalStore.sorting = true;
        await wrapper.vm.$nextTick(); // Wait for watcher

        expect(selectingStore.selectAll).toBe(false);
    });
});
