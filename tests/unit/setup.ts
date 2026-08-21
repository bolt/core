import Vue from 'vue';
import $ from 'jquery';
import { afterEach, beforeEach, expect, vi } from 'vitest';
import { resetDom } from './helpers/dom';
import { installWarningHandler, resetWarnings, reviewWarnings } from './helpers/warnings';

Vue.config.productionTip = false;
Vue.config.devtools = false;

// Several components reach for the global jQuery rather than importing it:
// Collection.vue uses `window.$` directly, and assets/js/jquery.js — which sets
// the globals in the browser build — is never imported by a component.
(window as unknown as Record<string, unknown>).$ = $;
(window as unknown as Record<string, unknown>).jQuery = $;

// Date.vue's updated() hook calls both on the flatpickr input. jsdom's
// constraint-validation support is partial, so make sure they exist.
if (!HTMLInputElement.prototype.reportValidity) {
    HTMLInputElement.prototype.reportValidity = () => true;
}
if (!HTMLInputElement.prototype.setCustomValidity) {
    HTMLInputElement.prototype.setCustomValidity = () => undefined;
}

// jsdom implements no innerText at all. Collection.vue uses it to strip markup
// out of a collection item's title before displaying it. textContent is close
// enough here: the source node is detached and unstyled, so the two agree.
if (!Object.getOwnPropertyDescriptor(HTMLElement.prototype, 'innerText')) {
    Object.defineProperty(HTMLElement.prototype, 'innerText', {
        configurable: true,
        get(this: HTMLElement) {
            return this.textContent;
        },
        set(this: HTMLElement, value: string) {
            this.textContent = value;
        },
    });
}

// jsdom has no layout, so it implements no scrollIntoView. ajax-save.js scrolls
// the validation errors into view after a rejected save.
if (!Element.prototype.scrollIntoView) {
    Element.prototype.scrollIntoView = () => undefined;
}

installWarningHandler();

beforeEach(() => {
    resetWarnings();
});

afterEach(() => {
    const { unexpected, missing } = reviewWarnings();
    resetWarnings();

    resetDom();
    localStorage.clear();
    vi.clearAllTimers();
    vi.useRealTimers();
    vi.unstubAllGlobals();

    // Select.vue keeps two module-less caches on `window`; without this they
    // leak between tests and a cached response answers the wrong assertion.
    delete (window as unknown as Record<string, unknown>).selectCache;
    delete (window as unknown as Record<string, unknown>).requestCache;

    expect(unexpected, `Unexpected Vue warning(s):\n${unexpected.join('\n')}`).toEqual([]);
    expect(missing, `Expected Vue warning(s) that never happened:\n${missing.join('\n')}`).toEqual([]);
});
