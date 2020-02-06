<template>
  <div>
    <textarea
      v-model="val"
      class="form-control field--textarea"
      :name="name"
      rows="10"
      :required="required == 1"
      :readonly="readonly"
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
    required: Number,
    readonly: Boolean,
  },
};
</script>
