import type { Pinia } from 'pinia';
import { mount } from '@vue/test-utils';
import SidebarToggler from '@/sidebar/Components/_SidebarToggler.vue';
import { describe, it, expect, beforeEach } from 'vitest';
import { createPinia, setActivePinia } from 'pinia';
import { useGeneralStore } from '@/sidebar/store';

describe('SidebarToggler Component', () => {
    let generalStore: ReturnType<typeof useGeneralStore>;
    let pinia: Pinia;

    beforeEach(() => {
        pinia = createPinia();
        setActivePinia(pinia);
        generalStore = useGeneralStore();
        localStorage.clear();
        document.body.innerHTML = '<div class="admin"></div>';
    });

    const defaultProps = {
        labels: { toggler: 'Toggle Sidebar' },
    };

    const mountComponent = () => {
        return mount(SidebarToggler, {
            props: defaultProps,
            global: {
                plugins: [pinia],
            },
        });
    };

    it('renders correctly', () => {
        const wrapper = mountComponent();
        expect(wrapper.find('button.admin__sidebar--slim').exists()).toBe(true);
        expect(wrapper.find('.sr-only').text()).toBe('Toggle Sidebar');
    });

    it('toggles slim sidebar on click', async () => {
        const wrapper = mountComponent();

        expect(generalStore.slimSidebar).toBe(false);

        await wrapper.find('button').trigger('click');
        expect(generalStore.slimSidebar).toBe(true);

        await wrapper.find('button').trigger('click');
        expect(generalStore.slimSidebar).toBe(false);
    });

    it('updates DOM and localStorage when slim changes', async () => {
        const wrapper = mountComponent();

        generalStore.slimSidebar = true;
        await wrapper.vm.$nextTick();

        expect(document.querySelector('.admin')?.classList.contains('is-slim')).toBe(true);
        expect(localStorage.getItem('slim-sidebar')).toBe('true');

        generalStore.slimSidebar = false;
        await wrapper.vm.$nextTick();

        expect(document.querySelector('.admin')?.classList.contains('is-slim')).toBe(false);
        expect(localStorage.getItem('slim-sidebar')).toBe('false');
    });

    it('applies restored slim state immediately on mount', () => {
        generalStore.slimSidebar = true;

        mountComponent();

        expect(document.querySelector('.admin')?.classList.contains('is-slim')).toBe(true);
        expect(localStorage.getItem('slim-sidebar')).toBe('true');
    });
});
