<template>
    <div class="input-group">
        <input
            :id="id"
            v-model="val"
            :name="name"
            class="form-control"
            type="number"
            :step="step"
            :min="min"
            :max="max"
            :required="required"
            :readonly="readonly"
            :data-errormessage="typeof errormessage === 'string' ? errormessage : undefined"
            :pattern="typeof pattern === 'string' ? pattern : undefined"
            :placeholder="typeof placeholder === 'string' ? placeholder : undefined"
        />
    </div>
</template>

<script setup lang="ts">
import { useFieldValue } from '../composables/useFieldValue';

const props = defineProps<{
    id?: string;
    value?: string;
    name?: string;
    step?: number | string;
    min?: number;
    max?: number;
    type?: string;
    disabled?: boolean;
    required?: boolean;
    readonly?: boolean;
    errormessage?: string | boolean; //string if errormessage is set, and false otherwise
    pattern?: string | boolean;
    placeholder?: string | boolean;
}>();

const { val } = useFieldValue(props.value);
</script>
