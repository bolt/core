<template>
  <div>
    <markdown-editor
      :id="name"
      v-model="val"
      :name="name"
      :configs="config"
    ></markdown-editor>
  </div>
</template>

<script>
import markdownEditor from 'vue-simplemde/src/markdown-editor';

export default {
  name: 'EditorMarkdown',
  components: {
    markdownEditor,
  },
  props: ['value', 'label', 'name'],
  data: () => {
    return {
      val: null,
      config: {
        spellChecker: false,
        status: false,
        toggleFullScreen: true,
      },
    };
  },
  mounted() {
    this.val = this.$options.filters.strip(this.value);
  },
};
</script>

<style scoped>
@import '~simplemde/dist/simplemde.min.css';
</style>
