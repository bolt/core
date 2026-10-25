import { config } from '@vue/test-utils';

/**
 * Captures errors thrown inside Vue lifecycle hooks and event handlers.
 *
 * Vue does not let a hook error propagate to the caller — it routes it through
 * `app.config.errorHandler`, or logs it when no handler is set. Our global
 * warnHandler turns warnings into failures (see ../setup.ts), so a spec that
 * deliberately exercises a throwing hook has to intercept it here.
 *
 * Every mount() copies test-utils' global config onto its own app, so the
 * component has to be mounted inside `run` for the handler to reach it.
 */
export async function captureVueErrors(run: () => void | Promise<void>): Promise<Error[]> {
    const captured: Error[] = [];
    const previous = config.global.config.errorHandler;

    config.global.config.errorHandler = (error: unknown) => {
        captured.push(error as Error);
    };

    try {
        await run();
    } finally {
        config.global.config.errorHandler = previous;
    }

    return captured;
}
