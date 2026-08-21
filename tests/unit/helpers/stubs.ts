/**
 * Stubs for the third-party components the app embeds.
 *
 * These are keyed by the *tag name used in the SFC template*, not by package
 * name — deliberately. vue-multiselect, vuedraggable, vue-trumbowyg and
 * vue-easymde all change their prop and event contracts across major versions,
 * and all four are due a bump at the Vue 3 upgrade. Stubbing the tag rather
 * than `vi.mock()`ing the package confines that churn here.
 *
 * vue-flatpickr-component is deliberately absent: Date.vue reaches into the DOM
 * flatpickr builds, so Date.spec.ts drives the real library instead.
 *
 * Each stub owns the v-model contract of the thing it replaces and exposes a
 * plainly-named method for driving it (`choose`, `reorder`, `type`). Specs call
 * those methods, so when `value`/`input` becomes `modelValue`/
 * `update:modelValue` only this file changes.
 */

/** vue-multiselect. Renders the `option`, `singleLabel` and `tag` scoped slots. */
export const multiselectStub = {
    name: 'Multiselect',
    props: {
        value: { default: null },
        multiple: { type: Boolean, default: false },
        options: { type: Array, default: () => [] },
        taggable: { type: Boolean, default: false },
        disabled: { type: Boolean, default: false },
    },
    computed: {
        tagOptions(this: { value: unknown }) {
            return Array.isArray(this.value) ? this.value : [];
        },
    },
    methods: {
        /** Pick an option, as clicking one in the real dropdown would. */
        choose(this: { $emit: (event: string, payload: unknown) => void }, option: unknown) {
            this.$emit('input', option);
        },
        /** Add a new tag, as typing one and confirming would. */
        tag(this: { $emit: (event: string, payload: unknown) => void }, label: string) {
            this.$emit('tag', label);
        },
    },
    template: `
        <div class="multiselect-stub">
            <div v-if="!multiple && value" class="stub-single-label">
                <slot name="singleLabel" :option="value"></slot>
            </div>
            <div v-for="(option, index) in options" :key="'option-' + index" class="stub-option">
                <slot name="option" :option="option"></slot>
            </div>
            <div v-for="(option, index) in tagOptions" :key="'tag-' + index" class="stub-tag">
                <slot name="tag" :option="option"></slot>
            </div>
        </div>
    `,
};

/** vuedraggable. Renders its default slot and can emit a reordered list. */
export const draggableStub = {
    name: 'Draggable',
    props: {
        value: { type: Array, default: () => [] },
    },
    methods: {
        /** Emit a new order, as finishing a drag would. */
        reorder(this: { $emit: (event: string, payload: unknown) => void }, list: unknown[]) {
            this.$emit('input', list);
        },
    },
    template: '<div class="draggable-stub"><slot></slot></div>',
};

/** vue-trumbowyg. A textarea standing in for the rich text editor. */
export const trumbowygStub = {
    name: 'Trumbowyg',
    props: {
        value: { default: '' },
        id: { type: String, default: '' },
        name: { type: String, default: '' },
        config: { type: Object, default: () => ({}) },
    },
    methods: {
        /** Replace the editor contents, as typing into it would. */
        type(this: { $emit: (event: string, payload: unknown) => void }, html: string) {
            this.$emit('input', html);
        },
    },
    template: `
        <textarea
            class="trumbowyg-stub"
            :id="id"
            :name="name"
            :value="value"
            @input="$emit('input', $event.target.value)"
        ></textarea>
    `,
};

/** vue-easymde. A textarea standing in for the markdown editor. */
export const easyMdeStub = {
    name: 'VueEasymde',
    props: {
        value: { default: '' },
        id: { type: String, default: '' },
        name: { type: String, default: '' },
        configs: { type: Object, default: () => ({}) },
    },
    methods: {
        /** Replace the editor contents, as typing into it would. */
        type(this: { $emit: (event: string, payload: unknown) => void }, markdown: string) {
            this.$emit('input', markdown);
        },
    },
    template: `
        <textarea
            class="easymde-stub"
            :id="id"
            :name="name"
            :value="value"
            @input="$emit('input', $event.target.value)"
        ></textarea>
    `,
};
