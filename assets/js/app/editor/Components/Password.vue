<template>
    <div>
        <div class="input-group">
            <input
                :id="id"
                ref="inputField"
                class="form-control"
                type="password"
                :name="name"
                :value="value"
                :required="required"
                :readonly="readonly"
                :data-errormessage="errormessage"
                :pattern="pattern"
                :placeholder="placeholder"
                autocomplete="new-password"
                @input="measureStrength"
            />

            <div class="input-group-text p-0">
                <i ref="visibilityToggle" class="toggle-password fas fa-eye" @click="togglePassword"></i>
            </div>
        </div>
        <progress-bar v-if="strength" ref="progressBar" :max="4" height="4px"></progress-bar>
    </div>
</template>

<script>
import ProgressBar from './ProgressBar';
import $ from 'jquery';

export default {
    name: 'EditorPassword',

    components: {
        ProgressBar,
    },

    props: {
        value: String,
        name: String,
        id: String,
        hidden: Boolean,
        strength: Boolean,
        required: Boolean,
        readonly: Boolean,
        errormessage: String | Boolean, //string if errormessage is set, and false otherwise
        pattern: String | Boolean,
        placeholder: String | Boolean,
    },

    mounted: function() {
        // this.val = this.$options.filters.strip(this.value);
        if (!this.hidden) {
            this.$refs.visibilityToggle.click();
        }
        if (this.value && this.strength) {
            this.$refs.inputField.dispatchEvent(new Event('input'));
        }
    },

    methods: {
        togglePassword(event) {
            const iconElement = event.target;
            const inputElement = $('#' + this.id)[0];
            const inputType = inputElement.getAttribute('type');

            if (inputType === 'password') {
                inputElement.setAttribute('type', 'text');
                iconElement.classList.replace('fa-eye', 'fa-eye-slash');
            } else if (inputType === 'text') {
                inputElement.setAttribute('type', 'password');
                iconElement.classList.replace('fa-eye-slash', 'fa-eye');
            }
        },
        measureStrength(event) {
            const inputElement = event.target;
            if (this.strength) {
                let result = window.zxcvbn(inputElement.value);
                this.$refs.progressBar.value = result.score;
            }
        },
    },
};
</script>
