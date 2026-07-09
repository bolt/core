import type { Pinia } from 'pinia';
import { mount } from '@vue/test-utils';
import Checkbox from '@/listing/Components/Table/Row/_Checkbox.vue';
import { describe, it, expect, beforeEach } from 'vitest';
import { createPinia, setActivePinia } from 'pinia';
import { useSelectingStore, useGeneralStore } from '@/listing/store';

describe('Row Checkbox Component', () => {
    let selectingStore: ReturnType<typeof useSelectingStore>;
    let generalStore: ReturnType<typeof useGeneralStore>;
    let pinia: Pinia;

    beforeEach(() => {
        pinia = createPinia();
        setActivePinia(pinia);
        selectingStore = useSelectingStore();
        generalStore = useGeneralStore();
    });

    const defaultProps = {
        id: 123,
    };

    const mountComponent = () => {
        return mount(Checkbox, {
            props: defaultProps,
            global: {
                plugins: [pinia],
            },
        });
    };

    it('renders correctly when not sorting', () => {
        const wrapper = mountComponent();

        expect(wrapper.find('.listing--checkbox').isVisible()).toBe(true);
        expect(wrapper.find('input[type="checkbox"]').exists()).toBe(true);
        expect(wrapper.find('label').attributes('for')).toBe('row-123');
    });

    it('hides when sorting is true', async () => {
        generalStore.sorting = true;
        const wrapper = mountComponent();
        await wrapper.vm.$nextTick();

        expect(wrapper.find('.listing--checkbox').isVisible()).toBe(false);
    });

    it('updates selected state based on selectAll store value', async () => {
        const wrapper = mountComponent();
        const input = wrapper.find('input[type="checkbox"]');

        expect((input.element as HTMLInputElement).checked).toBe(false);

        selectingStore.selectAll = true;
        await wrapper.vm.$nextTick();

        expect((input.element as HTMLInputElement).checked).toBe(true);
    });

    it('calls select and deSelect when clicked', async () => {
        const wrapper = mountComponent();
        const input = wrapper.find('input[type="checkbox"]');

        await input.setValue(true);
        expect(selectingStore.selected).toContain(123);

        await input.setValue(false);
        expect(selectingStore.selected).not.toContain(123);
    });

    it('deselects when sorting becomes true', async () => {
        const wrapper = mountComponent();
        const input = wrapper.find('input[type="checkbox"]');

        await input.setValue(true);
        expect(selectingStore.selected).toContain(123);

        generalStore.sorting = true;
        await wrapper.vm.$nextTick();

        expect((input.element as HTMLInputElement).checked).toBe(false);
        expect(selectingStore.selected).not.toContain(123);
    });
});
