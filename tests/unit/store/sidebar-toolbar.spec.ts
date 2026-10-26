import { describe, expect, it } from 'vitest';
import { createSidebarStore, createToolbarStore } from '../helpers/store';

describe('sidebar store', () => {
    it('starts with a full-width sidebar', () => {
        expect(createSidebarStore().stores.general.slimSidebar).toBe(false);
    });

    it('can be seeded as slim', () => {
        expect(createSidebarStore({ general: { slimSidebar: true } }).stores.general.slimSidebar).toBe(true);
    });
});

describe('toolbar store', () => {
    it('starts with no colour', () => {
        expect(createToolbarStore().stores.general.toolbarColor).toBe('');
    });

    it('can be seeded with a colour', () => {
        expect(createToolbarStore({ general: { toolbarColor: '#112233' } }).stores.general.toolbarColor).toBe(
            '#112233',
        );
    });
});
