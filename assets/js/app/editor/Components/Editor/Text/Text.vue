<template>
  <div>
    <input
      :id="id"
      class="form-control"
      :class="getType"
      :name="name"
      placeholder="…"
      type="text"
      v-model="val"
      :disabled="disabled == 1"
    />
  </div>
</template>

<script>
import field from '../../../mixins/value';

export default {
  name: 'editor-text',
  props: ['value', 'label', 'name', 'type', 'disabled', 'id'],
  mixins: [field],
  mounted() {
    this.$root.$on('generate-from-title', data => (this.generate = data));
  },
  data: () => {
    return {
      generate: false,
    };
  },
  watch: {
    val() {
      if (this.generate) {
        this.$root.$emit('slugify-from-title');
      }
    },
  },
  computed: {
    getType() {
      if (this.type === 'large') {
        return 'form-control-lg';
      }
    },
  },
};
</script>
