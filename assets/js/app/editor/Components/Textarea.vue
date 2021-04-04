<template>
    <div>
        <textarea
            v-model="rawVal"
            class="form-control field--textarea"
            :name="name"
            :rows="rows"
            :required="required"
            :readonly="readonly"
            :data-errormessage="errormessage"
            :placeholder="placeholder"
            :style="{ height: styleHeight }"
            :maxlength="maxlength"
            :title="name"
        ></textarea>
    </div>
</template>

<script>
import field from '../mixins/value';

export default {
    name: 'EditorTextarea',
    mixins: [field],
    props: {
        value: String,
        name: String,
        required: Boolean,
        readonly: Boolean,
        errormessage: String | Boolean,
        placeholder: String | Boolean,
        height: String | Number,
        maxlength: String,
    },
    data() {
        return {
            // If height is 50px/vh/vw/%, set css height.
            // If height is 50 (a number), set rows attribute.
            rows: isNaN(this.height) ? false : this.height,
            styleHeight: isNaN(this.height) ? this.height : false,
        };
    },
};
</script>
