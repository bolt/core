<template>
  <div class="form-group">
      <label>{{ label }}</label>
      {{val}}
  </div>
</template>

<script>
import field from '../../helpers/mixins/fieldValue';

export default {
  name: "editor-image",
  props: ['value', 'label', 'name'],
  mixins: [field],
};
</script>