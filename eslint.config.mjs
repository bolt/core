import js from '@eslint/js';
import cypress from 'eslint-plugin-cypress';
import prettierRecommended from 'eslint-plugin-prettier/recommended';
import sonarjs from 'eslint-plugin-sonarjs';
import vue from 'eslint-plugin-vue';
import vuejsAccessibility from 'eslint-plugin-vuejs-accessibility';
import globals from 'globals';
import tseslint from 'typescript-eslint';

export default [
    {
        ignores: ['node_modules/**', 'public/assets/**', 'var/**', 'vendor/**'],
    },

    js.configs.recommended,

    ...tseslint.configs.recommended,

    ...vue.configs['flat/recommended'],

    ...vuejsAccessibility.configs['flat/recommended'],

    sonarjs.configs.recommended,

    {
        files: ['assets/**/*.{js,ts,vue}'],
        languageOptions: {
            // The SFCs use <script setup lang="ts">, so vue-eslint-parser hands
            // their script blocks to the TypeScript parser.
            parserOptions: {
                parser: tseslint.parser,
                extraFileExtensions: ['.vue'],
            },
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
            '@typescript-eslint/no-this-alias': 'off',
            '@typescript-eslint/no-require-imports': 'off',
            'vuejs-accessibility/label-has-for': [
                'error',
                {
                    required: {
                        some: ['nesting', 'id'],
                    },
                },
            ],
        },
    },

    {
        files: ['assets/js/app/editor/Components/File.vue', 'assets/js/app/editor/Components/Image.vue'],
        rules: {
            'vuejs-accessibility/no-static-element-interactions': 'off',
        },
    },

    {
        files: ['assets/js/app/editor/Components/Text.vue'],
        rules: {
            'vuejs-accessibility/no-autofocus': 'off',
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
