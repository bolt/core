<template>
  <div>
    <input
      class="form-control" 
      :class="type"
      :name="name" 
      placeholder="…" 
      type="text" 
      v-model="val" 
    >
  </div>
</template>

<script>
import field from '../../helpers/mixins/fieldValue';

export default {
  name: "editor-text",
  props: ['value', 'label', 'name', 'type'],
  mixins: [field],
  mounted() {
    this.$root.$on('generate-from-title', data => this.generate = data);
  },
  data: () => {
    return {
      generate: false
    };
  },
  watch: {
    val(){
      if(this.generate){
        this.$root.$emit('slugify-from-title');
      }
    }
  }
};
</script>