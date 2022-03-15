<template>
    <div class="input-group">
        <input
            :id="id"
            v-model="val"
            :name="name"
            class="form-control"
            type="number"
            :step="step"
            :required="required"
            :readonly="readonly"
            :data-errormessage="errormessage"
            :pattern="pattern"
            :placeholder="placeholder"
        />
    </div>
</template>

<script>
import val from '../mixins/value';

export default {
    name: 'EditorNumber',
    mixins: [val],
    props: {
        id: String,
        value: String,
        name: String,
        step: Number | String,
        type: String,
        disabled: Boolean,
        required: Boolean,
        readonly: Boolean,
        errormessage: String | Boolean, //string if errormessage is set, and false otherwise
        pattern: String | Boolean,
        placeholder: String | Boolean,
    },
};
</script>
