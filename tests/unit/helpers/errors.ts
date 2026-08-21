import Vue from 'vue';

/**
 * Captures errors thrown inside Vue lifecycle hooks.
 *
 * Vue does not let a hook error propagate to the caller — it routes it through
 * `config.errorHandler`, or logs it as a warning when no handler is set. Our
 * global warnHandler turns warnings into failures (see ../setup.ts), so a spec
 * that deliberately exercises a throwing hook has to intercept it here.
 */
export async function captureVueErrors(run: () => void | Promise<void>): Promise<Error[]> {
    const captured: Error[] = [];
    const previous = Vue.config.errorHandler;

    Vue.config.errorHandler = (error: Error) => {
        captured.push(error);
    };

    try {
        await run();
    } finally {
        Vue.config.errorHandler = previous;
    }

    return captured;
}
