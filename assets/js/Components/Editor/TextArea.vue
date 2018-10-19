<template>
  <div class="form-group">
      <label>{{ label }}</label>
      <textarea 
        :name="name" 
        v-bind="val"
        cols="25"
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