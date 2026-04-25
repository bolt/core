import js from '@eslint/js';
import vuePlugin from 'eslint-plugin-vue';
import prettierPlugin from 'eslint-plugin-prettier';
import prettierConfig from 'eslint-config-prettier';
import cypressPlugin from 'eslint-plugin-cypress';
import globals from 'globals';
import vueParser from 'vue-eslint-parser';
import babelParser from '@babel/eslint-parser';

export default [
    js.configs.recommended,
    ...vuePlugin.configs['flat/recommended'],
    {
        plugins: {
            prettier: prettierPlugin,
            cypress: cypressPlugin,
        },
        languageOptions: {
            globals: {
                ...globals.browser,
                ...globals.node,
                ...cypressPlugin.configs.globals.globals,
            },
            parser: vueParser,
            parserOptions: {
                parser: babelParser,
                ecmaVersion: 2017,
                sourceType: 'module',
                requireConfigFile: false,
            },
        },
        settings: {
            'import/resolver': {
                node: {
                    extensions: ['.js', '.vue'],
                },
            },
        },
        rules: {
            ...prettierConfig.rules,
            ...prettierPlugin.configs.recommended.rules,
            'no-console': ['error', { allow: ['error', 'warn'] }],
            'no-debugger': 'error',
            'vue/require-default-prop': 'off',
            'vue/require-prop-type-constructor': 'off',
            'prettier/prettier': ['error', { printWidth: 120 }],
        },
    },
    {
        files: ['**/*.vue'],
        languageOptions: {
            parser: vueParser,
        },
    },
];
