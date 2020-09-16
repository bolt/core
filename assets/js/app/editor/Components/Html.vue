<template>
  <div>
    <trumbowyg :id="name" v-model="val" :name="name" :config="config"></trumbowyg>
  </div>
</template>

<script>
import trumbowyg from 'vue-trumbowyg';
import 'trumbowyg/dist/ui/trumbowyg.css';

export default {
  name: 'EditorHtml',
  components: {
    trumbowyg,
  },
  props: {
    value: String,
    label: String,
    name: String,
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
  mounted() {
    this.val = this.$options.filters.strip(this.value);
  },
};
</script>
