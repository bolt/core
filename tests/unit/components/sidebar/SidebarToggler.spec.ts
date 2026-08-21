import { mount } from '@vue/test-utils';
import { nextTick } from 'vue';
import { describe, expect, it } from 'vitest';
import SidebarToggler from '@/sidebar/Components/_SidebarToggler.vue';
import { createSidebarStore } from '../../helpers/store';
import { withAdminShell } from '../../helpers/dom';

const propsData = { version: '6.1.7', aboutLink: '/bolt/about', labels: { toggler: 'Toggle sidebar' } };

const mountToggler = (slimSidebar = false) => {
    const testStore = createSidebarStore({ general: { slimSidebar } });
    return {
        ...testStore,
        wrapper: mount(SidebarToggler, { localVue: testStore.localVue, store: testStore.store, propsData }),
    };
};

describe('SidebarToggler', () => {
    it('renders a labelled toggle button', () => {
        const { wrapper } = mountToggler();

        expect(wrapper.find('button.admin__sidebar--slim').exists()).toBe(true);
        expect(wrapper.find('.sr-only').text()).toBe('Toggle sidebar');
    });

    it('asks the store to go slim when clicked', async () => {
        withAdminShell();

        const { wrapper, dispatched } = mountToggler();
        await wrapper.find('button').trigger('click');

        expect(dispatched('general/slimSidebar')).toEqual([true]);
    });

    it('asks the store to go wide again when already slim', async () => {
        withAdminShell();

        const { wrapper, dispatched } = mountToggler(true);
        await wrapper.find('button').trigger('click');

        expect(dispatched('general/slimSidebar')).toEqual([false]);
    });

    it('marks the admin shell slim and remembers the choice', async () => {
        withAdminShell();

        const { store } = mountToggler();
        await store.dispatch('general/slimSidebar', true);
        await nextTick();

        expect(document.querySelector('.admin')!.classList.contains('is-slim')).toBe(true);
        expect(localStorage.getItem('slim-sidebar')).toBe('true');
    });

    it('unmarks the admin shell when going wide again', async () => {
        withAdminShell();

        const { store } = mountToggler(true);
        await store.dispatch('general/slimSidebar', false);
        await nextTick();

        expect(document.querySelector('.admin')!.classList.contains('is-slim')).toBe(false);
        expect(localStorage.getItem('slim-sidebar')).toBe('false');
    });
});
