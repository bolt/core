import { mount } from '@vue/test-utils';
import { nextTick } from 'vue';
import { describe, expect, it } from 'vitest';
import SidebarToggler from '@/sidebar/Components/_SidebarToggler.vue';
import { createSidebarStore } from '../../helpers/store';
import { withAdminShell } from '../../helpers/dom';

const props = { version: '6.1.7', aboutLink: '/bolt/about', labels: { toggler: 'Toggle sidebar' } };

const mountToggler = (slimSidebar = false) => {
    const testStore = createSidebarStore({ general: { slimSidebar } });
    const written = testStore.writes('general', 'slimSidebar');
    return {
        ...testStore,
        written,
        wrapper: mount(SidebarToggler, { global: { plugins: [testStore.pinia] }, props }),
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

        const { wrapper, written } = mountToggler();
        await wrapper.find('button').trigger('click');

        expect(written).toEqual([true]);
    });

    it('asks the store to go wide again when already slim', async () => {
        withAdminShell();

        const { wrapper, written } = mountToggler(true);
        await wrapper.find('button').trigger('click');

        expect(written).toEqual([false]);
    });

    it('marks the admin shell slim and remembers the choice', async () => {
        withAdminShell();

        const { stores } = mountToggler();
        stores.general.slimSidebar = true;
        await nextTick();

        expect(document.querySelector('.admin')!.classList.contains('is-slim')).toBe(true);
        expect(localStorage.getItem('slim-sidebar')).toBe('true');
    });

    it('unmarks the admin shell when going wide again', async () => {
        withAdminShell();

        const { stores } = mountToggler(true);
        stores.general.slimSidebar = false;
        await nextTick();

        expect(document.querySelector('.admin')!.classList.contains('is-slim')).toBe(false);
        expect(localStorage.getItem('slim-sidebar')).toBe('false');
    });
});
