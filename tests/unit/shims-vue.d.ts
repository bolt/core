/**
 * SFCs are still plain JS (no `lang="ts"`), so tsc cannot infer anything from
 * them. This tells it that a `.vue` import is a Vue component, which is enough
 * for the specs to mount one.
 *
 * At the Vue 3 upgrade this becomes `DefineComponent` from 'vue', and once the
 * SFCs are `<script setup lang="ts">` the checker becomes vue-tsc and the specs
 * get real prop types instead.
 */
declare module '*.vue' {
    import Vue from 'vue';
    const component: typeof Vue;
    export default component;
}

/**
 * vue-flatpickr-component 8 ships no types. Date.spec.ts uses the component
 * both as a mount target and as a findComponent selector.
 */
declare module 'vue-flatpickr-component' {
    import Vue from 'vue';
    const component: typeof Vue;
    export default component;
}
