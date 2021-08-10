<template>
    <div>
        <input
            :title="name"
            :id="id"
            v-model="rawVal"
            :title="name"
            class="form-control"
            :class="getType"
            :name="name"
            type="text"
            :disabled="disabled"
            :required="required"
            :readonly="readonly"
            :data-errormessage="errormessage"
            :pattern="pattern"
            :placeholder="placeholder"
            :autofocus="autofocus == true"
        />
    </div>
</template>

<script>
import field from '../mixins/value';

export default {
    name: 'EditorText',
    mixins: [field],
    props: {
        value: String,
        name: String,
        type: String,
        disabled: Boolean,
        id: String,
        required: Boolean,
        readonly: Boolean,
        errormessage: String | Boolean,
        pattern: String | Boolean,
        placeholder: String | Boolean,
        autofocus: Boolean,
    },
    data: () => {
        return {
            generate: false,
        };
    },
    computed: {
        getType() {
            if (this.type === 'large') {
                return 'form-control-lg';
            }

            return this.type;
        },
    },
    watch: {
        rawVal() {
            if (this.generate) {
                this.$root.$emit('slugify-from-title');
            }
        },
    },
    mounted() {
        this.$root.$on('generate-from-title', data => (this.generate = data));
    },
};
</script>
