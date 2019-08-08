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
          <label for="embed-url">{{ labels.content_url }}</label>
          <div class="input-group">
            <input v-model="url" class="form-control" :name="name + '[url]'" :placeholder="labels.placeholder_content_url" type="url">
            <span class="input-group-btn">
              <button class="btn btn-default refresh" type="button" disabled=""><i class="fa fa-refresh"></i></button>
            </span>
          </div>
        </div>
        <div class="form-group form-inline">
          <label for="embed-width-size">{{ labels.label_size }}</label>
          <input class="form-control col-2 offset-1" :name="name + '[width]'" type="number" :value="width"> ×
          <label for="embed-height-size" class="sr-only">{{ labels.label_height }}</label>
          <input class="form-control col-2" :name="name + '[height]'" type="number" :value="height">
          <label>{{ labels.label_pixel }}</label>
        </div>
        <div class="form-group">
          <label>{{ labels.label_matched_embed }}</label>
          <input class="form-control title" :name="name + '[title]'" readonly="" title="Title" type="text" :value="title">
          <input class="form-control author_name" :name="name + '[authorname]'" readonly="" title="Author" type="text" :value="authorname">
          <input class="author_url" :name="name + '[authorurl]'" type="hidden" :value="author_url">
          <input class="html" :name="name + '[html]'" type="hidden" :value="html">
          <input class="thumbnail_url" :name="name + '[thumbnail]'" type="hidden" :value="thumbnail">
        </div>
      </div>
      <div class="col-4">
        <label>{{ labels.label_preview }}</label>
        <div class="editor__image--preview">
          <a
            class="editor__image--preview-image"
            :href="previewImage"
            :style="`background-image: url('${previewImage}')`"
          >
          </a>
        </div>
      </div>
    </div>
  </div>
</div>
</template>

<script>
import _ from 'lodash';
import baguetteBox from 'baguettebox.js';
import field from '../../../mixins/value';

export default {
  name: 'editor-embed',
  props: [
    'embedapi',
    'label',
    'name',
    'authorurl',
    'authorname',
    'height',
    'html',
    'thumbnail',
    'title',
    'url',
    'width',
    'labels',
  ],
  mixins: [field],
  mounted() {
    this.previewImage = this.thumbnail;
  },
  data: () => {
    return {
      authorurl: null,
      authorname: null,
      height: null,
      html: null,
      thumbnail: null,
      title: null,
      url: null,
      width: null,
    };
  },
  watch: {
    url: function (newValue, oldValue) {
      if (! newValue) { return; }
      this.debouncedFetchEmbed();
    }
  },
  created: function () {
    this.debouncedFetchEmbed = _.debounce(this.fetchEmbed, 500);
    this.previewImage = this.thumbnail;
  },
  methods: {
    fetchEmbed: function() {
      const body = new FormData();
      body.append('url', this.url);
      body.append( '_csrf_token',  document.getElementsByName('_csrf_token')[0].value)

      fetch(this.embedapi, { method: 'POST', body: body })
      .then(response => response.json())
      .then(json => {
        this.authorurl    = json.author_url;
        this.authorname   = json.author_name;
        this.height       = json.height;
        this.html         = json.html;
        this.thumbnail    = json.thumbnail_url;
        this.title        = json.title;
        //this.url          = json.url;
        this.width        = json.width;
        this.previewImage = json.thumbnail_url;
      })
      .catch((err) => {
        console.log(err);
      });
    }
  },
  updated() {
    baguetteBox.run('.editor__image--preview', {
      afterShow: () => {
        noScroll.on();
      },
      afterHide: () => {
        noScroll.off();
      },
    });
  },
};
</script>
