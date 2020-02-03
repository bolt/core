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
    />
  </div>
</template>

<script>
import val from '../mixins/value';

export default {
  name: 'EditorNumber',
  mixins: [val],
  props: [
    'value',
    'label',
    'name',
    'step',
    'type',
    'disabled',
    'required',
    'readonly',
  ],
};
</script>
