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
                @change="liveValue = $event.target.checked"
            />
            <label class="custom-control-label form-label" :for="name">{{ label }}</label>

            <!-- This hidden input is actually what gets submitted. It submits "true" when checked, and "false" when not checked -->
            <!-- It exists because we need an "unchecked" value submitted. See https://developer.mozilla.org/en-US/docs/Web/HTML/Element/input/checkbox -->
            <input type="hidden" :value="liveValue" :name="name" />
        </div>
    </div>
</template>

<script>
export default {
    name: 'EditorCheckbox',
    props: {
        value: {
            type: Boolean,
        },
        name: {
            type: String,
        },
        id: {
            type: String,
        },
        required: {
            type: Boolean,
        },
        readonly: {
            type: Boolean,
        },
        label: {
            type: String,
        },
        mode: {
            type: String,
        },
    },
    data() {
        return {
            liveValue: this.value,
        };
    },
};
</script>
