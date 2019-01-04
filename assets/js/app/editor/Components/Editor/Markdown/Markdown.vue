<template>
  <div>
    <markdown-editor
      :name="name"
      :id="name"
      v-model="val"
      :configs="config"
    ></markdown-editor>
  </div>
</template>

<script>
import markdownEditor from 'vue-simplemde/src/markdown-editor';

export default {
  name: 'editor-markdown',
  props: ['value', 'label', 'name'],
  components: {
    markdownEditor,
  },
  mounted() {
    this.val = this.$options.filters.strip(this.value);
  },
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
};
</script>

<style scoped>
@import '~simplemde/dist/simplemde.min.css';
</style>
