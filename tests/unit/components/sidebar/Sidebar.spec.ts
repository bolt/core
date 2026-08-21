import { mount } from '@vue/test-utils';
import { describe, expect, it } from 'vitest';
import Sidebar from '@/sidebar/Components/Sidebar.vue';
import { createSidebarStore } from '../../helpers/store';
import { captureVueErrors } from '../../helpers/errors';

const propsData = {
    menu: [{ name: 'Dashboard', icon: 'fa-home', link: '/bolt', submenu: null }],
    version: '6.1.7',
    aboutLink: '/bolt/about',
    labels: { toggler: 'Toggle sidebar', 'action.new': 'New', 'action.view': 'View' },
};

const mountSidebar = () => {
    const testStore = createSidebarStore();
    return {
        ...testStore,
        wrapper: mount(Sidebar, { localVue: testStore.localVue, store: testStore.store, propsData }),
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

        const { store, dispatched } = mountSidebar();

        expect(dispatched('general/slimSidebar')).toEqual([true]);
        expect(store.getters['general/slimSidebar']).toBe(true);
    });

    it('leaves the sidebar wide when the stored value is false', () => {
        localStorage.setItem('slim-sidebar', 'false');

        const { store, dispatched } = mountSidebar();

        expect(dispatched('general/slimSidebar')).toEqual([]);
        expect(store.getters['general/slimSidebar']).toBe(false);
    });

    it('leaves the sidebar wide when nothing is stored', () => {
        const { dispatched } = mountSidebar();

        expect(dispatched('general/slimSidebar')).toEqual([]);
    });

    it('fails to restore the sidebar when the stored value is not valid JSON', async () => {
        // Pinning current behaviour: created() runs JSON.parse on the raw
        // localStorage value with no guard. Vue swallows the resulting
        // SyntaxError, so the sidebar still renders but silently loses the
        // remembered state instead of falling back to the default.
        localStorage.setItem('slim-sidebar', 'not json');
        const { store, localVue } = createSidebarStore();
        let wrapper!: ReturnType<typeof mount>;

        const errors = await captureVueErrors(() => {
            wrapper = mount(Sidebar, { localVue, store, propsData });
        });

        expect(errors).toHaveLength(1);
        expect(errors[0]).toBeInstanceOf(SyntaxError);
        expect(wrapper.find('nav.admin__sidebar--nav').exists()).toBe(true);
        expect(store.getters['general/slimSidebar']).toBe(false);
    });
});
