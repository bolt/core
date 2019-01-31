<template>
<div>
  <!-- .field--embed -->
  <div
    class="editor__embed"
    :name="name"
    :id="name">
    <div class="row">
      <div class="col-8">
        <div class="form-group">
          <label for="embed-url">URL of content to embed</label>
          <div class="input-group">
            <input class="form-control" :name="name + '[url]'" placeholder="URL of content on Facebook, Twitter, Soundcloud, Youtube, Vimeo…" type="url" :value="url">
            <span class="input-group-btn">
              <button class="btn btn-default refresh" type="button" disabled=""><i class="fa fa-refresh"></i></button>
            </span>
          </div>
        </div>
        <div class="form-group form-inline">
          <label for="embed-width-size">Size</label>
          <input class="form-control col-2 offset-1" :name="name + '[width]'" type="number" :value="width"> ×
          <label for="embed-height-size" class="sr-only">Height</label>
          <input class="form-control col-2" :name="name + '[height]'" type="number" :value="height">
          <label>pixel</label>
        </div>
        <div class="form-group">
          <label>Matched Embed</label>
          <input class="form-control title" :name="name + '[title]'" readonly="" title="Title" type="text" :value="title">
          <input class="form-control author_name" :name="name + '[authorname]'" readonly="" title="Author" type="text" :value="authorname">
          <input class="author_url" :name="name + '[author_url]'" type="hidden" :value="author_url">
          <input class="html" :name="name + '[html]'" type="hidden" :value="html">
          <input class="thumbnail_url" :name="name + '[thumbnail]'" type="hidden" :value="thumbnail">
        </div>
      </div>
      <div class="col-4">
        <label>Preview</label>
        <div class="editor__image--preview">
          <a
            class="editor__image--preview-image"
            :style="`background-image: url('${previewImage}')`"
        </div>
      </div>
    </div>
  </div>
</div>
</template>

<script>
import field from '../../../mixins/value';

export default {
  name: 'editor-embed',
  props: [
    'label',
    'name',
    'author_url',
    'authorname',
    'height',
    'html',
    'thumbnail',
    'title',
    'url',
    'width',
  ],
  mixins: [field],
  mounted() {
    this.previewImage = this.thumbnail;
  },
  updated() {

  },
  data: () => {
    return {
      author_url: null,
      authorname: null,
      height: null,
      html: null,
      thumbnail: null,
      title: null,
      url: null,
      width: null,
    };
  },
  methods: {

  },
  computed: {
  },
};
</script>

<style scoped>
</style>
