<template>
  <div class="input-group">
    <input
      v-model="val"
      :name="name"
      class="form-control"
      placeholder="…"
      type="number"
      :step="step"
      :required="required == 1"
      :readonly="readonly"
      :data-errormessage="errormessage"
    />
  </div>
</template>

<script>
import val from '../mixins/value';

export default {
  name: 'EditorNumber',
  mixins: [val],
  props: {
    value: String,
    name: String,
    step: Number,
    type: String,
    disabled: Boolean,
    required: Number,
    readonly: Boolean,
    errormessage: String | Boolean, //string if errormessage is set, and false otherwise
  },
};
</script>
