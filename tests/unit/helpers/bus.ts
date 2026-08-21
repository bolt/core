import type Vue from 'vue';

/**
 * The Slug ⇄ Text event bus.
 *
 * Vue 2.7 has no dedicated bus: Slug.vue and Text.vue talk over `this.$root`,
 * which every component on the page shares. Note that test-utils mounts the
 * component under test inside a parent, so `$root` is that parent rather than
 * the component — which means `wrapper.emitted()` does *not* see bus traffic
 * and it has to be observed here instead.
 *
 * `$root.$emit`/`$on` is removed in Vue 3; the upgrade replaces it with a mitt
 * instance, at which point only this file changes.
 */

type Wrapper = { vm: Vue };

/** Emits on the shared bus, as another field on the page would. */
export function emitOnBus(wrapper: Wrapper, event: string, ...payload: unknown[]): void {
    wrapper.vm.$root.$emit(event, ...payload);
}

/**
 * Starts recording one bus event. The returned array collects the arguments of
 * each emission, in order — the same shape as `wrapper.emitted(event)`.
 */
export function recordBus(wrapper: Wrapper, event: string): unknown[][] {
    const emissions: unknown[][] = [];
    wrapper.vm.$root.$on(event, (...args: unknown[]) => emissions.push(args));
    return emissions;
}
