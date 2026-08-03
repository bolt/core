import js from '@eslint/js';
import cypress from 'eslint-plugin-cypress';
import prettierRecommended from 'eslint-plugin-prettier/recommended';
import vue from 'eslint-plugin-vue';
import globals from 'globals';

export default [
    {
        ignores: ['node_modules/**', 'public/assets/**', 'var/**', 'vendor/**'],
    },

    js.configs.recommended,

    // The codebase is Vue 2.7, so use the vue2 preset rather than the Vue 3
    // default. This is the flat-config equivalent of the old
    // "plugin:vue/recommended" under eslint-plugin-vue 6.
    ...vue.configs['flat/vue2-recommended'],

    {
        files: ['assets/**/*.{js,vue}'],
        languageOptions: {
            ecmaVersion: 2022,
            sourceType: 'module',
            globals: {
                ...globals.browser,
                ...globals.node,
            },
        },
        rules: {
            'no-console': ['error', { allow: ['error', 'warn'] }],
            'no-debugger': 'error',
            'vue/require-default-prop': 'off',
            'vue/require-prop-type-constructor': 'off',
            // Component names and their kebab-case registrations are the public
            // contract used by the Twig (in-DOM) templates; renaming them would
            // be a breaking change, so these two stay off.
            'vue/multi-word-component-names': 'off',
            'vue/component-definition-name-casing': 'off',
        },
    },

    // These three components write directly to their own props. That is a real
    // bug class, but fixing it means reworking how each field propagates its
    // value, which is a behaviour change and out of scope for a tooling PR.
    // Scoped to the known offenders on purpose, so any *new* prop mutation
    // elsewhere still fails the lint run.
    {
        files: [
            'assets/js/app/editor/Components/File.vue',
            'assets/js/app/editor/Components/Image.vue',
            'assets/js/app/editor/Components/Select.vue',
        ],
        rules: {
            'vue/no-mutating-props': 'off',
        },
    },

    // ESLint 10 adds no-useless-assignment, which flags a dead folderPath
    // initialiser in these two components. Removing it is safe but it is still
    // a change to runtime code, and this PR deliberately touches runtime code
    // by formatting only. Deferred with the other findings above.
    {
        files: ['assets/js/app/editor/Components/File.vue', 'assets/js/app/editor/Components/Image.vue'],
        rules: {
            'no-useless-assignment': 'off',
        },
    },

    {
        files: ['tests/cypress/**/*.js'],
        ...cypress.configs.recommended,
    },

    // Must stay last: disables every stylistic rule that would fight Prettier
    // and enables prettier/prettier. Formatting options live in .prettierrc.
    prettierRecommended,
];
