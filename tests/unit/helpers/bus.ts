import type { Emitter } from 'mitt';
import { eventBus, type Events } from '@/eventBus';

/**
 * The Slug ⇄ Text event bus.
 *
 * Slug.vue and Text.vue talk over the mitt bus in assets/js/app/eventBus.ts.
 * It is a module singleton shared by every component, so `wrapper.emitted()`
 * does *not* see its traffic and it has to be observed here instead. The
 * global teardown in setup.ts clears its handlers after each test.
 */

/** Emits on the shared bus, as another field on the page would. */
export const emitOnBus: Emitter<Events>['emit'] = eventBus.emit;

/**
 * Starts recording one bus event. The returned array collects the payload of
 * each emission, in order.
 */
export function recordBus<Key extends keyof Events>(event: Key): Events[Key][] {
    const emissions: Events[Key][] = [];
    eventBus.on(event, payload => emissions.push(payload));
    return emissions;
}

/** How many handlers are listening for one bus event. */
export function busListeners(event: keyof Events): number {
    return eventBus.all.get(event)?.length ?? 0;
}
