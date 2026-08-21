import { defineConfig } from 'vitest/config';
import vue2 from '@vitejs/plugin-vue2';
import path from 'node:path';
import { fileURLToPath } from 'node:url';

const root = path.dirname(fileURLToPath(import.meta.url));

export default defineConfig({
    plugins: [vue2()],
    resolve: {
        // Components import each other without the extension (`from './_SubMenu'`),
        // which webpack resolves via its own `resolve.extensions`. Vite does not
        // include .vue by default, so mirror the build here rather than editing
        // the imports.
        extensions: ['.mjs', '.js', '.mts', '.ts', '.jsx', '.tsx', '.json', '.vue'],
        alias: [
            { find: '@', replacement: path.resolve(root, 'assets/js/app') },
            // Collection.vue calls Vue.compile(), which only the full build ships.
            { find: /^vue$/, replacement: 'vue/dist/vue.esm.js' },
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
        include: ['tests/unit/**/*.spec.ts'],
        exclude: ['**/node_modules/**', '**/tests/cypress/**'],
        // @vue/test-utils v1 is CJS and reaches for vue-template-compiler.
        server: { deps: { inline: ['@vue/test-utils'] } },
        coverage: {
            provider: 'v8',
            reporter: ['text', 'html'],
            include: ['assets/js/app/**', 'assets/js/filters/**', 'assets/js/services/**'],
            exclude: [
                '**/*.d.ts',
                // Boot files: each only mounts `new Vue()` against a
                // Twig-rendered element, so there is nothing to assert that
                // Cypress does not already cover end to end. Listed one by one
                // rather than globbed as `*/index.js` — that glob also caught
                // notifications/index.js, which mounts nothing and is real
                // behaviour.
                'assets/js/app/editor/index.js',
                'assets/js/app/listing/index.js',
                'assets/js/app/sidebar/index.js',
                'assets/js/app/toolbar/index.js',
                'assets/js/app/login/index.js',
                // The store singletons, for the same reason: each is a single
                // `new Vuex.Store(...)` wired at boot. The modules they compose
                // are covered in tests/unit/store.
                'assets/js/app/*/store/index.js',
                // Registered nowhere — listing/store/index.js builds itself from
                // the general, listing and selecting modules only. Dead code.
                'assets/js/app/listing/store/modules/type/**',
            ],
            // The gate is 90% across the board.
            //
            // Deliberately not `perFile: true` yet. Per-file would be the
            // stronger gate — an average lets a well-covered file offset a bare
            // one — but a handful of files cannot reach 90 on their own without
            // changes to assets/, which this branch does not make:
            //
            //   Date.vue            the locale is loaded with a template-literal
            //                       `require()`, which has no meaning in an ES
            //                       module and cannot run under Vite at all
            //   save-on-ctrl-s.js   guards on `!$('form#editcontent')`, and a
            //                       jQuery object is never falsy, so the early
            //                       return is unreachable
            //   Image.vue,          the baguetteBox afterShow/afterHide
            //   Embed.vue           callbacks only run from the real lightbox
            //
            // Worth turning on once the Vue 3 migration has dealt with those.
            thresholds: {
                statements: 90,
                branches: 90,
                functions: 90,
                lines: 90,
            },
        },
    },
});
