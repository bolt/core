import $ from 'jquery';

/**
 * Resolves once every jQuery ready handler registered so far has run.
 *
 * The scripts under assets/js/app do their work in `$(document).ready(...)`.
 * With the document already loaded, jQuery still runs those handlers on a
 * later timer turn, which test-utils' flushPromises (a setImmediate) resolves
 * ahead of. jQuery runs ready handlers in registration order, so one registered
 * after the script's own resolves only once the script's has run.
 */
export function jqueryReady(): Promise<void> {
    return new Promise(resolve => {
        $(() => resolve());
    });
}
