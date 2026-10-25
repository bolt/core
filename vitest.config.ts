import { defineConfig } from 'vitest/config';
import vue from '@vitejs/plugin-vue';
import path from 'node:path';
import { fileURLToPath } from 'node:url';

const root = path.dirname(fileURLToPath(import.meta.url));

export default defineConfig({
    plugins: [vue()],
    resolve: {
        // Components import each other without the extension (`from './_SubMenu'`),
        // which webpack resolves via its own `resolve.extensions`. Vite does not
        // include .vue by default, so mirror the build here rather than editing
        // the imports.
        extensions: ['.mjs', '.js', '.mts', '.ts', '.jsx', '.tsx', '.json', '.vue'],
        alias: [
            { find: '@', replacement: path.resolve(root, 'assets/js/app') },
            // Collection.vue renders `{ template }` components built at runtime,
            // which only the build that ships the template compiler can do.
            { find: /^vue$/, replacement: 'vue/dist/vue.esm-bundler.js' },
            // Markdown.vue's `@import '~easymde/dist/easymde.min.css'` — the webpack
            // `~` prefix means "resolve from node_modules", which Vite does natively.
            { find: /^~/, replacement: '' },
        ],
    },
    test: {
        environment: 'jsdom',
        // Specs import describe/it/expect from 'vitest' explicitly.
        globals: false,
        setupFiles: ['tests/unit/setup.ts'],
        // Undo every vi.spyOn() and vi.stubGlobal() after each test, so a
        // failing assertion cannot leave a spy or a stub behind for the next.
        restoreMocks: true,
        unstubGlobals: true,
        include: ['tests/unit/**/*.spec.ts'],
        exclude: ['**/node_modules/**', '**/tests/cypress/**'],
        coverage: {
            provider: 'v8',
            reporter: ['text', 'html'],
            include: ['assets/js/app/**', 'assets/js/filters/**', 'assets/js/services/**'],
            exclude: [
                '**/*.d.ts',
                // Boot files: each only mounts `createApp()` against a
                // Twig-rendered element, so there is nothing to assert that
                // Cypress does not already cover end to end. Listed one by one
                // rather than globbed as `*/index.*` — that glob also caught
                // notifications/index.js, which mounts nothing and is real
                // behaviour, and the Pinia stores in */store/index.ts.
                'assets/js/app/index.ts',
                'assets/js/app/editor/index.ts',
                'assets/js/app/listing/index.ts',
                'assets/js/app/sidebar/index.ts',
                'assets/js/app/toolbar/index.ts',
                'assets/js/app/login/index.js',
            ],
            // The gate is 95% for statements, functions and lines, and 90% for
            // branches: the TypeScript components guard every optional prop
            // with `??` and `?.`, and each of those counts as a branch.
            //
            // Deliberately not `perFile: true` yet. Per-file would be the
            // stronger gate — an average lets a well-covered file offset a bare
            // one — but save-on-ctrl-s.js cannot reach it without a change to
            // assets/: it guards on `!$('form#editcontent')`, and a jQuery
            // object is never falsy, so the early return is unreachable.
            thresholds: {
                statements: 95,
                branches: 90,
                functions: 95,
                lines: 95,
            },
        },
    },
});
