import { mount } from '@vue/test-utils';
import { describe, expect, it } from 'vitest';
import Sidebar from '@/sidebar/Components/Sidebar.vue';
import { createSidebarStore } from '../../helpers/store';
import { captureVueErrors } from '../../helpers/errors';

const props = {
    menu: [{ name: 'Dashboard', icon: 'fa-home', link: '/bolt', submenu: null }],
    version: '6.1.7',
    aboutLink: '/bolt/about',
    labels: { toggler: 'Toggle sidebar', 'action.new': 'New', 'action.view': 'View' },
};

const mountSidebar = () => {
    const testStore = createSidebarStore();
    const written = testStore.writes('general', 'slimSidebar');
    return {
        ...testStore,
        written,
        wrapper: mount(Sidebar, { global: { plugins: [testStore.pinia] }, props }),
    };
};

describe('Sidebar', () => {
    it('renders the menu, the toggler and the version footer', () => {
        const { wrapper } = mountSidebar();

        expect(wrapper.find('ul.admin__sidebar--menu').exists()).toBe(true);
        expect(wrapper.find('button.admin__sidebar--slim').exists()).toBe(true);

        const footerLink = wrapper.find('.admin__sidebar--footer a');
        expect(footerLink.attributes('href')).toBe('/bolt/about');
        expect(footerLink.text()).toContain('6.1.7');
    });

    it('restores a remembered slim sidebar on creation', () => {
        localStorage.setItem('slim-sidebar', 'true');

        const { stores, written } = mountSidebar();

        expect(written).toEqual([true]);
        expect(stores.general.slimSidebar).toBe(true);
    });

    it('leaves the sidebar wide when the stored value is false', () => {
        localStorage.setItem('slim-sidebar', 'false');

        const { stores, written } = mountSidebar();

        expect(written).toEqual([]);
        expect(stores.general.slimSidebar).toBe(false);
    });

    it('leaves the sidebar wide when nothing is stored', () => {
        const { written } = mountSidebar();

        expect(written).toEqual([]);
    });

    it('falls back to a wide sidebar when the stored value is not valid JSON', async () => {
        localStorage.setItem('slim-sidebar', 'not json');
        const { pinia, stores } = createSidebarStore();
        let wrapper!: ReturnType<typeof mount>;

        const errors = await captureVueErrors(() => {
            wrapper = mount(Sidebar, { global: { plugins: [pinia] }, props });
        });

        expect(errors).toEqual([]);
        expect(wrapper.find('ul.admin__sidebar--menu').exists()).toBe(true);
        expect(stores.general.slimSidebar).toBe(false);
    });
});
