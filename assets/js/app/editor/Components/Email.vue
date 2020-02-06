<template>
  <div>
    <input
      :id="id"
      class="form-control"
      :name="name"
      :value="value"
      type="email"
      :required="required == 1"
      :readonly="readonly"
      :data-errormessage="errormessage"
    />
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
      type: String
    },
    id: {
      type: String,
    },
    required: {
      type: Number,
    },
    readonly: {
      type: Boolean,
    },
    errormessage: {
      type: String | Boolean, //string if errormessage is set, and false otherwise
    }
  },
};
</script>
