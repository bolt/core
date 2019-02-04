<template>
  <div class="input-group">
    <input
      v-model="val"
      :name="name"
      class="form-control"
      placeholder="…"
      type="number"
      :step="step"
    />
  </div>
</template>

<script>
import val from '../../../mixins/value';

export default {
  name: 'EditorNumber',
  mixins: [val],
  props: ['value', 'label', 'name', 'step', 'type', 'disabled'],
};
</script>
