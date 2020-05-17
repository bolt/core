<template>
  <div>
    <textarea
      v-model="val"
      class="form-control field--textarea"
      :name="name"
      rows="10"
      :required="required"
      :readonly="readonly"
      :data-errormessage="errormessage"
      :pattern="pattern"
      :placeholder="placeholder"
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
    pattern: String | Boolean,
    placeholder: String | Boolean,
  },
};
</script>
