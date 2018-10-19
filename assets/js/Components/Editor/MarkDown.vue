<template>
  <div class="form-group">
      <label>{{ label }}</label>
      <markdown-editor :name="name" :id="name" v-model="val" :configs="config"></markdown-editor>
  </div>
</template>

<script>
import field from '../../helpers/mixins/fieldValue';
import markdownEditor from 'vue-simplemde/src/markdown-editor'

export default {
  name: "editor-markdown",
  props: ['value', 'label', 'name'],
  mixins: [field],
  components: {
    markdownEditor
  },
  data: () => {
    return {
      config:{
        spellChecker: false,
        status: false,
        toggleFullScreen: true
      }
    };
  },
};
</script>

<style scoped>
  @import '~simplemde/dist/simplemde.min.css';
</style>