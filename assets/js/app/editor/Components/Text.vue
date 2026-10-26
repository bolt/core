<template>
    <div>
        <input
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
import { eventBus } from '../../eventBus';

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
                eventBus.emit('slugify-from-title');
            }
        },
    },
    mounted() {
        eventBus.on('generate-from-title', this.setGenerate);
    },
    beforeDestroy() {
        eventBus.off('generate-from-title', this.setGenerate);
    },
    methods: {
        setGenerate(active) {
            this.generate = active;
        },
    },
};
</script>
