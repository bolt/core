import type { Pinia } from 'pinia';
import { mount } from '@vue/test-utils';
import Sidebar from '@/sidebar/Components/Sidebar.vue';
import { describe, it, expect, beforeEach } from 'vitest';
import { createPinia, setActivePinia } from 'pinia';
import { useGeneralStore } from '@/sidebar/store';

describe('Sidebar Component', () => {
    let generalStore: ReturnType<typeof useGeneralStore>;
    let pinia: Pinia;

    beforeEach(() => {
        pinia = createPinia();
        setActivePinia(pinia);
        generalStore = useGeneralStore();
        localStorage.clear();
    });

    const defaultProps = {
        menu: [{ name: 'Dashboard', link: '/dashboard' }],
        version: '4.0.0',
        aboutLink: '/about',
        labels: { about: 'About Bolt' },
    };

    const mountComponent = () => {
        return mount(Sidebar, {
            props: defaultProps,
            global: {
                plugins: [pinia],
                stubs: {
                    'sidebar-menu': true,
                    'sidebar-toggler': true,
                },
            },
        });
    };

    it('renders correctly', () => {
        const wrapper = mountComponent();

        expect(wrapper.find('nav.admin__sidebar--nav').exists()).toBe(true);

        const footer = wrapper.find('footer.admin__sidebar--footer');
        expect(footer.exists()).toBe(true);
        expect(footer.find('a').attributes('href')).toBe('/about');
        expect(footer.text()).toContain('Bolt version 4.0.0');

        expect(wrapper.findComponent({ name: 'sidebar-menu' }).exists()).toBe(true);
        expect(wrapper.findComponent({ name: 'sidebar-toggler' }).exists()).toBe(true);
    });

    it('sets slimSidebar from localStorage if true', () => {
        localStorage.setItem('slim-sidebar', 'true');

        mountComponent();

        expect(generalStore.slimSidebar).toBe(true);
    });

    it('sets slimSidebar from localStorage if false', () => {
        localStorage.setItem('slim-sidebar', 'false');

        mountComponent();

        expect(generalStore.slimSidebar).toBe(false);
    });

    it('does not set slimSidebar if localStorage is empty', () => {
        generalStore.slimSidebar = false;

        mountComponent();

        expect(generalStore.slimSidebar).toBe(false); // Default is false, it shouldn't change
    });
});
