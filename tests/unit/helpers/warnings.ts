import Vue from 'vue';

/**
 * Vue warnings are failures by default (see ../setup.ts). A spec that
 * deliberately exercises a component in a state Vue complains about declares
 * the expected warning here, which both silences the failure and asserts that
 * the warning actually happened.
 */

const expected: RegExp[] = [];
const seen: string[] = [];

/**
 * Warnings that are pre-existing defects in the Vue 2.7 components rather than
 * problems with a test. All are fixed on refactor/vue3-composition-api; when a
 * fix lands, delete its entry and the warning starts failing again.
 */
const KNOWN_DEFECTS = [
    // `type: String | Boolean` on ~10 props. The bitwise OR of two constructors
    // evaluates to 0, so Vue rejects it as a type and prop validation is dead
    // for those props. The fix is to declare `type: [String, Boolean]`.
    /Invalid prop type: "0" is not a constructor/,
    // File.vue, Image.vue and Select.vue write to their own props. Already
    // whitelisted for `vue/no-mutating-props` in eslint.config.mjs.
    /Avoid mutating a prop directly/,
];

export function installWarningHandler(): void {
    Vue.config.warnHandler = (message: string) => {
        seen.push(message);
    };
}

/** Declares a warning this test expects. Fails if it does not occur. */
export function expectVueWarning(pattern: RegExp): void {
    expected.push(pattern);
}

export function resetWarnings(): void {
    expected.length = 0;
    seen.length = 0;
}

/**
 * Returns the unexpected warnings, and the expected patterns that never fired.
 */
export function reviewWarnings(): { unexpected: string[]; missing: RegExp[] } {
    const allowed = [...KNOWN_DEFECTS, ...expected];
    const unexpected = seen.filter(message => !allowed.some(pattern => pattern.test(message)));
    const missing = expected.filter(pattern => !seen.some(message => pattern.test(message)));

    return { unexpected, missing };
}
