import Vue, { watch } from 'vue';
import { createPinia, PiniaVuePlugin, setActivePinia, type Pinia } from 'pinia';
import { createLocalVue } from '@vue/test-utils';

import { useGeneralStore as useListingGeneralStore, useListingStore, useSelectingStore } from '@/listing/store';
import { useGeneralStore as useEditorGeneralStore } from '@/editor/store';
import { useGeneralStore as useSidebarGeneralStore } from '@/sidebar/store';
import { useGeneralStore as useToolbarGeneralStore } from '@/toolbar/store';

/**
 * Store factories.
 *
 * These build a *real* Pinia instance out of the application's own stores, so
 * the real getters and actions run — only the initial state is seeded.
 *
 * Each factory makes a fresh Pinia and makes it the active one, so no state is
 * shared across specs. Pass `localVue` and `pinia` to mount() so the component
 * resolves the same stores the spec seeded and observes.
 *
 * The components write most of their state straight onto the store rather than
 * going through an action, so there are two recorders: `writes()` for state
 * assignments and `calls()` for actions.
 */

type AnyStore = {
    $id: string;
    $patch(state: Record<string, unknown>): void;
    $onAction(callback: (context: { name: string; args: unknown[] }) => void): () => void;
};

export interface TestStore<Stores extends Record<string, AnyStore>> {
    /** Pass to mount() as `pinia`. */
    pinia: Pinia;
    /** Pass to mount() as `localVue`. */
    localVue: typeof Vue;
    /** The stores themselves, for reading state back. */
    stores: Stores;
    /**
     * Every value written to `stores[store][key]`, in order. Only changes are
     * seen — writing the value a key already holds records nothing. Recording
     * starts at the first call, so call it before mount() to catch a write
     * made in `created`.
     */
    writes(store: keyof Stores, key: string): unknown[];
    /** Arguments of every call to the action `stores[store][action]`, in order. */
    calls(store: keyof Stores, action: string): unknown[][];
}

function build<Stores extends Record<string, AnyStore>>(
    create: () => Stores,
    seeds: Partial<Record<keyof Stores, Record<string, unknown>>>,
): TestStore<Stores> {
    const localVue = createLocalVue();
    localVue.use(PiniaVuePlugin);

    const pinia = createPinia();
    setActivePinia(pinia);

    const stores = create();
    for (const [name, seed] of Object.entries(seeds)) {
        // Cloned: callers routinely pass a fixture defined once at the top of
        // a spec, and actions like `select` push straight into state, which
        // would write back into that shared fixture.
        stores[name as keyof Stores].$patch(structuredClone(seed as Record<string, unknown>));
    }

    const written: Record<string, unknown[]> = {};
    const called: Record<string, unknown[][]> = {};

    return {
        pinia,
        localVue,
        stores,
        writes(store, key) {
            const id = `${String(store)}.${key}`;
            if (!written[id]) {
                written[id] = [];
                const target = stores[store] as unknown as Record<string, unknown>;
                watch(
                    () => target[key],
                    value => written[id].push(value),
                    { flush: 'sync' },
                );
            }
            return written[id];
        },
        calls(store, action) {
            const id = `${String(store)}.${action}`;
            if (!called[id]) {
                called[id] = [];
                stores[store].$onAction(({ name, args }) => {
                    if (name === action) {
                        called[id].push(args);
                    }
                });
            }
            return called[id];
        },
    };
}

export interface ListingSeed {
    general?: { type?: string | null; rowSize?: string; sorting?: boolean };
    listing?: { records?: unknown[] };
    selecting?: { selectAll?: boolean; selectedCount?: number; selected?: number[] };
}

export function createListingStore(seed: ListingSeed = {}) {
    return build(
        () => ({
            general: useListingGeneralStore(),
            listing: useListingStore(),
            selecting: useSelectingStore(),
        }),
        seed as Record<string, Record<string, unknown>>,
    );
}

/**
 * The editor's own store. Note no editor component reads it — the slim-sidebar
 * flag it holds is driven from the sidebar's store instead.
 */
export function createEditorStore(seed: { general?: { slimSidebar?: boolean } } = {}) {
    return build(() => ({ general: useEditorGeneralStore() }), seed);
}

export function createSidebarStore(seed: { general?: { slimSidebar?: boolean } } = {}) {
    return build(() => ({ general: useSidebarGeneralStore() }), seed);
}

export function createToolbarStore(seed: { general?: { toolbarColor?: string } } = {}) {
    return build(() => ({ general: useToolbarGeneralStore() }), seed);
}
