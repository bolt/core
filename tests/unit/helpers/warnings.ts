import { config } from '@vue/test-utils';

/**
 * Vue warnings are failures by default (see ../setup.ts). A spec that
 * deliberately exercises a component in a state Vue complains about declares
 * the expected warning here, which both silences the failure and asserts that
 * the warning actually happened.
 */

const expected: RegExp[] = [];
const seen: string[] = [];

/**
 * Installed on test-utils' global config rather than on an app, because every
 * mount() creates its own app and copies this config onto it.
 */
export function installWarningHandler(): void {
    config.global.config.warnHandler = (message: string) => {
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
    const unexpected = seen.filter(message => !expected.some(pattern => pattern.test(message)));
    const missing = expected.filter(pattern => !seen.some(message => pattern.test(message)));

    return { unexpected, missing };
}
