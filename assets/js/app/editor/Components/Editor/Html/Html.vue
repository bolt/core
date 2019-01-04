<template>
  <div>
    <trumbowyg
      :id="name"
      :name="name"
      v-model="val"
      :config="config"
    ></trumbowyg>
  </div>
</template>

<script>
import trumbowyg from 'vue-trumbowyg';
import 'trumbowyg/dist/ui/trumbowyg.css';

export default {
  name: 'editor-html',
  props: ['value', 'label', 'name'],
  components: {
    trumbowyg,
  },
  mounted() {
    this.val = this.$options.filters.strip(this.value);
  },
  data: () => {
    return {
      val: null,
      config: {
        btns: [
          // ['undo', 'redo'],
          ['formatting'],
          ['strong', 'em', 'del'],
          ['link'],
          ['insertImage'],
          ['justifyLeft', 'justifyCenter', 'justifyRight'],
          ['unorderedList', 'orderedList'],
          ['horizontalRule'],
          ['removeformat'],
          ['fullscreen'],
          ['viewHTML'],
        ],
      },
    };
  },
};
</script>
