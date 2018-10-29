<template>
  <div class="form-group">
      <label>{{ label }}</label>
      <textarea 
        class="form-control"
        :name="name" 
        v-model="val"
        rows="10"
      ></textarea>
  </div>
</template>

<script>
import field from '../../helpers/mixins/fieldValue';

export default {
  name: "editor-textarea",
  props: ['value', 'label', 'name'],
  mixins: [field],
};
</script>

<style scoped>
  textarea {
    resize: vertical;
  }
</style>