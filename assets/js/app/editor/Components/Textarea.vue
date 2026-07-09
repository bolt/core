<template>
    <div>
        <textarea
            :id="id"
            v-model="rawVal"
            class="form-control field--textarea"
            :name="name"
            :rows="rows"
            :required="required"
            :readonly="readonly"
            :data-errormessage="typeof errormessage === 'string' ? errormessage : undefined"
            :placeholder="typeof placeholder === 'string' ? placeholder : undefined"
            :style="{ height: styleHeight }"
            :maxlength="maxlength || undefined"
            :title="name"
        ></textarea>
    </div>
</template>

<script setup lang="ts">
import { computed } from 'vue';
import { useFieldValue } from '../composables/useFieldValue';

const props = defineProps<{
    id?: string;
    value?: string;
    // textarea.html.twig passes `:label`; the visible label is rendered by the
    // surrounding _base wrapper, so it's accepted here but not used internally.
    label?: string;
    name?: string;
    required?: boolean;
    readonly?: boolean;
    errormessage?: string | boolean;
    placeholder?: string | boolean;
    height?: string | number;
    maxlength?: string;
}>();

const { rawVal } = useFieldValue(props.value);

// If height is 50px/vh/vw/%, set css height.
// If height is 50 (a number), set rows attribute.
const heightIsNumeric = computed(() => !isNaN(props.height as number));
const rows = computed(() => (heightIsNumeric.value ? props.height : undefined));
const styleHeight = computed(() => (heightIsNumeric.value ? undefined : (props.height as string)));
</script>
