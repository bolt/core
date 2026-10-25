import type { Emitter } from 'mitt';
import { eventBus } from '@/eventBus';

/**
 * The Slug ⇄ Text event bus.
 *
 * Slug.vue and Text.vue talk over the shared mitt instance in
 * assets/js/app/eventBus.ts, which every component on the page imports. It is
 * not part of either component's emits, so `wrapper.emitted()` does *not* see
 * bus traffic and it has to be observed here instead.
 *
 * Listeners left behind are cleared after every test (see ../setup.ts).
 */

type BusEvents = typeof eventBus extends Emitter<infer Events> ? Events : never;

/** Emits on the shared bus, as another field on the page would. */
export function emitOnBus<K extends keyof BusEvents>(event: K, payload: BusEvents[K]): void {
    eventBus.emit(event, payload);
}

/**
 * Starts recording one bus event. The returned array collects the payload of
 * each emission, in order.
 */
export function recordBus<K extends keyof BusEvents>(event: K): BusEvents[K][] {
    const emissions: BusEvents[K][] = [];
    eventBus.on(event, payload => emissions.push(payload));
    return emissions;
}
