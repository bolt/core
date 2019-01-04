<template>
  <div class="input-group">
    <input
      :name="name"
      class="form-control"
      placeholder="…"
      type="number"
      :step="step"
      v-model="val"
    />
  </div>
</template>

<script>
import val from '../../../mixins/value';

export default {
  name: 'editor-number',
  props: ['value', 'label', 'name', 'step', 'type', 'disabled'],
  mixins: [val],
};
</script>
