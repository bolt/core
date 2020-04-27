<template>
  <div>
    <input
      :id="id"
      v-model="val"
      class="form-control"
      :class="getType"
      :name="name"
      type="text"
      :disabled="disabled == 1"
      :required="required == 1"
      :readonly="readonly == true"
      :data-errormessage="errormessage"
      :pattern="pattern"
      :placeholder="placeholder"
      :autofocus="autofocus == true"
    />
  </div>
</template>

<script>
import field from '../mixins/value';

export default {
  name: 'EditorText',
  mixins: [field],
  props: {
    value: String,
    name: String,
    type: String,
    disabled: Boolean,
    id: String,
    required: Number,
    readonly: Boolean,
    errormessage: String | Boolean,
    pattern: String | Boolean,
    placeholder: String | Boolean,
    autofocus: Boolean,
  },
  data: () => {
    return {
      generate: false,
    };
  },
  computed: {
    getType() {
      if (this.type === 'large') {
        return 'form-control-lg';
      }

      return '';
    },
  },
  watch: {
    val() {
      if (this.generate) {
        this.$root.$emit('slugify-from-title');
      }
    },
  },
  mounted() {
    this.$root.$on('generate-from-title', (data) => (this.generate = data));
  },
};
</script>
