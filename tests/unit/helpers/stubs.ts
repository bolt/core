/**
 * Stubs for the third-party components the app embeds.
 *
 * These are keyed by the *name the component registers itself under* —
 * `vue-multiselect`, `draggable`, `trumbowyg` — not by package. `<script setup>` uses an
 * imported component directly rather than resolving its template tag, so that
 * name is what test-utils matches a stub against. vue-multiselect, vuedraggable
 * and vue-trumbowyg change their prop and event contracts across major versions;
 * stubbing them rather than `vi.mock()`ing the package confines that churn here.
 *
 * vue-flatpickr-component is deliberately absent: Date.vue reaches into the DOM
 * flatpickr builds, so Date.spec.ts drives the real library instead. EasyMDE is
 * absent too: Markdown.vue constructs it directly rather than through a
 * component, so there is no tag to stub and Markdown.spec.ts mocks the module.
 *
 * Each stub owns the v-model contract of the thing it replaces and exposes a
 * plainly-named method for driving it (`choose`, `reorder`, `type`). Specs call those
 * methods, so when the contract changes only this file does.
 */

type Emit = (event: string, payload: unknown) => void;

/** vue-multiselect. Renders the `option`, `singleLabel` and `tag` scoped slots. */
export const multiselectStub = {
    name: 'Multiselect',
    props: {
        modelValue: { default: null },
        multiple: { type: Boolean, default: false },
        options: { type: Array, default: () => [] },
        taggable: { type: Boolean, default: false },
        disabled: { type: Boolean, default: false },
    },
    emits: ['update:modelValue', 'tag'],
    computed: {
        tagOptions(this: { modelValue: unknown }) {
            return Array.isArray(this.modelValue) ? this.modelValue : [];
        },
    },
    methods: {
        /** Pick an option, as clicking one in the real dropdown would. */
        choose(this: { $emit: Emit }, option: unknown) {
            this.$emit('update:modelValue', option);
        },
        /** Add a new tag, as typing one and confirming would. */
        tag(this: { $emit: Emit }, label: string) {
            this.$emit('tag', label);
        },
        /** The real component's public method, which the host calls through a ref. */
        removeElement(this: { $emit: Emit; modelValue: unknown }, option: unknown) {
            const selection = Array.isArray(this.modelValue) ? this.modelValue : [];
            this.$emit(
                'update:modelValue',
                selection.filter(item => item !== option),
            );
        },
    },
    template: `
        <div class="multiselect-stub">
            <div v-if="!multiple && modelValue" class="stub-single-label">
                <slot name="singleLabel" :option="modelValue"></slot>
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

/**
 * vuedraggable. Renders its `item` slot per element and can emit a reordered
 * list.
 */
export const draggableStub = {
    name: 'Draggable',
    props: {
        modelValue: { type: Array, default: () => [] },
        itemKey: { type: String, default: 'id' },
    },
    emits: ['update:modelValue'],
    methods: {
        /** Emit a new order, as finishing a drag would. */
        reorder(this: { $emit: Emit }, list: unknown[]) {
            this.$emit('update:modelValue', list);
        },
    },
    template: `
        <div class="draggable-stub">
            <template v-for="(element, index) in modelValue" :key="element[itemKey]">
                <slot name="item" :element="element" :index="index"></slot>
            </template>
        </div>
    `,
};

/** vue-trumbowyg. A textarea standing in for the rich text editor. */
export const trumbowygStub = {
    name: 'Trumbowyg',
    props: {
        modelValue: { default: '' },
        id: { type: String, default: '' },
        name: { type: String, default: '' },
        config: { type: Object, default: () => ({}) },
    },
    emits: ['update:modelValue'],
    methods: {
        /** Replace the editor contents, as typing into it would. */
        type(this: { $emit: Emit }, html: string) {
            this.$emit('update:modelValue', html);
        },
    },
    template: `
        <textarea
            class="trumbowyg-stub"
            :id="id"
            :name="name"
            :value="modelValue"
            @input="$emit('update:modelValue', $event.target.value)"
        ></textarea>
    `,
};
