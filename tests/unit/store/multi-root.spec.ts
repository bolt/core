import { mount } from '@vue/test-utils';
import { nextTick } from 'vue';
import { describe, expect, it } from 'vitest';
import Filter from '@/listing/Components/Filter.vue';
import Checkbox from '@/listing/Components/Table/Row/_Checkbox.vue';
import SidebarToggler from '@/sidebar/Components/_SidebarToggler.vue';
import { createListingStore, createSidebarStore } from '../helpers/store';
import { withAdminShell } from '../helpers/dom';

/**
 * The toolbar, sidebar and listing are separate Vue roots on one page, each
 * with its own Pinia. Every factory in helpers/store.ts makes its Pinia the
 * active one, so mounting a second root here leaves the *other* Pinia active —
 * as the last root to boot does in the admin.
 *
 * Vue 2 only, in what it guards: outside setup() and the lifecycle hooks — in
 * a watcher, an event handler — Vue 2.7 has no current instance, so a bare
 * `useXStore()` falls back to the active Pinia and would write into the wrong
 * root. That is why the components pass `this.$pinia`. Once they are
 * `<script setup>` on Vue 3, `useXStore()` runs in setup and always injects
 * its own app's Pinia, the argument goes, and these tests stay as they are.
 */
describe('stores across Vue roots', () => {
    it('keeps a listing write in its own root', async () => {
        const first = createListingStore({ general: { type: 'pages' } });
        const filter = mount(Filter, {
            localVue: first.localVue,
            pinia: first.pinia,
            propsData: { labels: { select_all: 'Select all' } },
        });
        mount(Checkbox, { localVue: first.localVue, pinia: first.pinia, propsData: { id: 7 } });

        const second = createListingStore({ general: { type: 'pages' } });
        mount(Filter, {
            localVue: second.localVue,
            pinia: second.pinia,
            propsData: { labels: { select_all: 'Select all' } },
        });

        // A click handler on Filter, then the watcher chain on Checkbox.
        await filter.find('#selectAll').trigger('click');
        await nextTick();

        expect(first.stores.selecting.selectAll).toBe(true);
        expect(first.stores.selecting.selected).toEqual([7]);
        expect(second.stores.selecting.selectAll).toBe(false);
        expect(second.stores.selecting.selected).toEqual([]);
    });

    it('keeps a sidebar write in its own root', async () => {
        withAdminShell();

        const sidebar = createSidebarStore();
        const toggler = mount(SidebarToggler, {
            localVue: sidebar.localVue,
            pinia: sidebar.pinia,
            propsData: { version: '6.1.7', aboutLink: '/bolt/about', labels: { toggler: 'Toggle sidebar' } },
        });

        // The listing boots after the sidebar, so its Pinia is the active one.
        createListingStore();

        await toggler.find('button').trigger('click');
        await nextTick();

        expect(sidebar.stores.general.slimSidebar).toBe(true);
        expect(document.querySelector('.admin')!.classList.contains('is-slim')).toBe(true);
    });
});
