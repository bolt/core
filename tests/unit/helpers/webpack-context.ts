/**
 * `import.meta.webpackContext()` for the test run.
 *
 * Webpack replaces the call at build time with a context over the matching
 * files. Vite has no equivalent, so the `webpack-context` plugin in
 * vitest.config.ts rewrites the call to this function, installed by setup.ts as
 * `globalThis.__webpackContext`. The plugin lists the directory in place of the
 * path it was given, as a map of `./<file>` to a lazy import of that file.
 *
 * Only the form the app uses is supported: a non-recursive, lazy context. Its
 * keys are `./<file>`, as webpack's are, and calling it with one imports that
 * file and resolves to the module.
 */

export type ContextFiles = Record<string, () => Promise<unknown>>;

export type WebpackContextOptions = {
    recursive?: boolean;
    regExp?: RegExp;
    mode?: string;
    chunkName?: string;
};

export type LazyWebpackContext = {
    (request: string): Promise<unknown>;
    keys(): string[];
};

/**
 * Imports one file of a context. A spec spies on `load` to see which file was
 * requested, or to make it fail to load, as a chunk can in the browser.
 */
export const contextLoader = {
    load: (request: string, importFile: () => Promise<unknown>): Promise<unknown> => importFile(),
};

export function webpackContext(files: ContextFiles, options: WebpackContextOptions = {}): LazyWebpackContext {
    const { recursive = true, regExp = /^\.\/.*$/, mode = 'sync' } = options;

    if (recursive || mode !== 'lazy') {
        throw new Error('Only non-recursive, lazy webpack contexts are supported in the unit tests.');
    }

    const keys = Object.keys(files).filter(key => regExp.test(key));

    const context = (request: string): Promise<unknown> => {
        if (!keys.includes(request)) {
            return Promise.reject(new Error(`Cannot find module '${request}'`));
        }

        return contextLoader.load(request, files[request]);
    };
    context.keys = () => [...keys];

    return context;
}
