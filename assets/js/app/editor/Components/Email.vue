<template>
    <div>
        <div class="input-group mb-2">
            <i class="input-group-text far fa-envelope me-0"></i>
            <input
                :id="id"
                class="form-control"
                :name="name"
                :value="value"
                type="email"
                :required="required"
                :readonly="readonly"
                :data-errormessage="errormessage"
                :pattern="pattern"
                :placeholder="placeholder"
            />
        </div>
    </div>
</template>

<script>
export default {
    name: 'EditorEmail',
    props: {
        value: {
            type: String,
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
        errormessage: {
            type: String | Boolean, //string if errormessage is set, and false otherwise
        },
        pattern: {
            type: String | Boolean,
        },
        placeholder: String | Boolean,
    },
};
</script>
