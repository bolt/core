<template>
  <div>
    <textarea
      class="form-control field--textarea"
      :name="name"
      v-model="val"
      rows="10"
    ></textarea>
  </div>
</template>

<script>
import field from '../../../mixins/value';

export default {
  name: 'editor-textarea',
  props: ['value', 'label', 'name'],
  mixins: [field],
};
</script>
