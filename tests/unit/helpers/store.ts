import Vue from 'vue';
import Vuex, { Store, type Module } from 'vuex';
import { createLocalVue } from '@vue/test-utils';
import { vi } from 'vitest';

import listingGeneral from '@/listing/store/modules/general/index.js';
import listingListing from '@/listing/store/modules/listing/index.js';
import listingSelecting from '@/listing/store/modules/selecting/index.js';
import editorGeneral from '@/editor/store/modules/general/index.js';
import sidebarGeneral from '@/sidebar/store/modules/general/index.js';
import toolbarGeneral from '@/toolbar/store/modules/general/index.js';

/**
 * Store factories.
 *
 * These build a *real* store out of the application's own modules, so the real
 * getters, mutations and actions run — only the initial state is seeded.
 *
 * Two things make this necessary rather than importing the store directly:
 *
 *  - Each `store/index.js` exports `new Vuex.Store(...)`, a module-level
 *    singleton, so importing it would share state across every spec in a run.
 *  - The modules export their state as a plain shared object (`const state =
 *    {...}; export default state`) rather than a factory, so even a fresh
 *    `new Vuex.Store()` would alias the same state object. Hence the clone.
 */

// The store modules are plain untyped JS, so this describes only the shape the
// helpers below need to read: the state to clone, and the rest passed straight
// through to Vuex.
type StoreModule = Module<Record<string, unknown>, unknown> & {
    state: Record<string, unknown>;
};

export interface TestStore {
    /** Pass to mount() as `store`. */
    store: Store<unknown>;
    /** Pass to mount() as `localVue`. */
    localVue: typeof Vue;
    /** Arguments of every `dispatch('<name>', payload)` call, in order. */
    dispatched(action: string): unknown[];
}

function seedModule(module: StoreModule, seed: Record<string, unknown> = {}): StoreModule {
    return {
        ...module,
        // A factory, so each store gets its own state object. The seed is
        // cloned too: callers routinely pass a fixture defined once at the top
        // of a spec, and mutations like `selecting/select` push straight into
        // state, which would write back into that shared fixture.
        state: () => ({ ...structuredClone(module.state), ...structuredClone(seed) }),
    } as unknown as StoreModule;
}

function build(modules: Record<string, StoreModule>, seeds: Record<string, Record<string, unknown>>): TestStore {
    const localVue = createLocalVue();
    localVue.use(Vuex);

    const seeded: Record<string, StoreModule> = {};
    for (const [name, module] of Object.entries(modules)) {
        seeded[name] = seedModule(module, seeds[name]);
    }

    const store = new Vuex.Store({ modules: seeded });
    const dispatch = vi.spyOn(store, 'dispatch');

    return {
        store,
        localVue,
        dispatched(action: string): unknown[] {
            const calls = dispatch.mock.calls as unknown as [string, unknown][];
            return calls.filter(call => call[0] === action).map(call => call[1]);
        },
    };
}

export interface ListingSeed {
    general?: { type?: string | null; rowSize?: string; sorting?: boolean };
    listing?: { records?: unknown[] };
    selecting?: { selectAll?: boolean; selectedCount?: number; selected?: unknown[] };
}

export function createListingStore(seed: ListingSeed = {}): TestStore {
    return build(
        {
            general: listingGeneral as StoreModule,
            listing: listingListing as StoreModule,
            selecting: listingSelecting as StoreModule,
        },
        seed as Record<string, Record<string, unknown>>,
    );
}

/**
 * The editor's own store. Note no editor component reads it — the slim-sidebar
 * flag it holds is driven from the sidebar's store instead.
 */
export function createEditorStore(seed: { general?: { slimSidebar?: boolean } } = {}): TestStore {
    return build({ general: editorGeneral as StoreModule }, seed as Record<string, Record<string, unknown>>);
}

export function createSidebarStore(seed: { general?: { slimSidebar?: boolean } } = {}): TestStore {
    return build({ general: sidebarGeneral as StoreModule }, seed as Record<string, Record<string, unknown>>);
}

export function createToolbarStore(seed: { general?: { toolbarColor?: string | null } } = {}): TestStore {
    return build({ general: toolbarGeneral as StoreModule }, seed as Record<string, Record<string, unknown>>);
}
