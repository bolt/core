import { describe, expect, it } from 'vitest';
import { createEditorStore, createSidebarStore, createToolbarStore } from '../helpers/store';

describe('sidebar store', () => {
    it('starts with a full-width sidebar', () => {
        expect(createSidebarStore().store.getters['general/slimSidebar']).toBe(false);
    });

    it('records the slim flag', async () => {
        const { store } = createSidebarStore();

        await store.dispatch('general/slimSidebar', true);

        expect(store.getters['general/slimSidebar']).toBe(true);
    });

    it('can be seeded as slim', () => {
        expect(createSidebarStore({ general: { slimSidebar: true } }).store.getters['general/slimSidebar']).toBe(true);
    });
});

describe('toolbar store', () => {
    it('starts with no colour', () => {
        expect(createToolbarStore().store.getters['general/toolbarColor']).toBeNull();
    });

    it('records the toolbar colour', async () => {
        const { store } = createToolbarStore();

        await store.dispatch('general/toolbarColor', '#112233');

        expect(store.getters['general/toolbarColor']).toBe('#112233');
    });
});

describe('editor store', () => {
    it('starts with a full-width sidebar', () => {
        expect(createEditorStore().store.getters['general/slimSidebar']).toBe(false);
    });

    it('records the slim flag', async () => {
        const { store } = createEditorStore();

        await store.dispatch('general/slimSidebar', true);

        expect(store.getters['general/slimSidebar']).toBe(true);
    });
});
