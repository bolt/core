<template>
    <div>
        <div :class="['custom-control', 'form-' + mode, 'form-check', mode == 'switch' ? 'form-switch' : '']">
            <input
                :id="id"
                class="form-check-input"
                :name="name"
                :checked="value"
                type="checkbox"
                :readonly="readonly"
                @change="liveValue = ($event.target as HTMLInputElement).checked"
            />
            <label class="custom-control-label form-label" :for="id">{{ label }}</label>

            <!-- This hidden input is actually what gets submitted. It submits "true" when checked, and "false" when not checked -->
            <!-- It exists because we need an "unchecked" value submitted. See https://developer.mozilla.org/en-US/docs/Web/HTML/Element/input/checkbox -->
            <input type="hidden" :value="liveValue" :name="name" />
        </div>
    </div>
</template>

<script setup lang="ts">
import { ref } from 'vue';

const props = defineProps<{
    value?: boolean;
    name?: string;
    id?: string;
    required?: boolean;
    readonly?: boolean;
    label?: string;
    mode?: string;
}>();

const liveValue = ref(props.value);
</script>
