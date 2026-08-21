import type Vue from 'vue';
import type { Store } from 'vuex';

/**
 * Vuex 3 declares the `store` mount option by augmenting Vue's ComponentOptions
 * in `vuex/types/vue.d.ts`, but nothing pulls that file into the program — its
 * own `index.d.ts` does not reference it. Without the augmentation tsc rejects
 * `mount(Component, { store })`, so it is restated here.
 *
 * Goes away with Vuex: Pinia is installed as a plugin, not a mount option.
 */
declare module 'vue/types/options' {
    // eslint-disable-next-line @typescript-eslint/no-unused-vars
    interface ComponentOptions<V extends Vue> {
        store?: Store<unknown>;
    }
}
