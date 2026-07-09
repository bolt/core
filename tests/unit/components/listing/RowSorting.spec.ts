import type { Pinia } from 'pinia';
import { mount } from '@vue/test-utils';
import Sorting from '@/listing/Components/Table/Row/_Sorting.vue';
import { describe, it, expect, beforeEach } from 'vitest';
import { createPinia, setActivePinia } from 'pinia';
import { useGeneralStore } from '@/listing/store';

describe('Sorting Component', () => {
    let generalStore: ReturnType<typeof useGeneralStore>;
    let pinia: Pinia;

    beforeEach(() => {
        pinia = createPinia();
        setActivePinia(pinia);
        generalStore = useGeneralStore();
    });

    const mountComponent = () => {
        return mount(Sorting, {
            global: {
                plugins: [pinia],
            },
        });
    };

    it('does not render when sorting is false', () => {
        const wrapper = mountComponent();

        expect(wrapper.find('button.listing__row--move').exists()).toBe(false);
    });

    it('renders when sorting is true', async () => {
        generalStore.sorting = true;
        const wrapper = mountComponent();
        await wrapper.vm.$nextTick();

        const btn = wrapper.find('button.listing__row--move');
        expect(btn.exists()).toBe(true);
        expect(btn.find('i.fa-equals').exists()).toBe(true);
    });
});
