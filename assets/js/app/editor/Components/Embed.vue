<template>
  <div>
    <!-- .field--embed -->
    <div :id="name" class="editor__embed" :name="name">
      <div class="row">
        <div class="col-8">
          <div class="form-group">
            <label for="embed-url">{{ labels.content_url }}</label>
            <div class="input-group">
              <input
                v-model="url"
                class="form-control"
                :name="name + '[url]'"
                :placeholder="labels.placeholder_content_url"
                type="url"
                :required="required == 1"
                :readonly="readonly"
                :data-errormessage="errormessage"
              />
              <span class="input-group-btn">
                <button
                  class="btn btn-default refresh"
                  type="button"
                  disabled=""
                >
                  <i class="fa fa-refresh"></i>
                </button>
              </span>
            </div>
          </div>
          <div class="form-group form-inline">
            <label for="embed-width-size">{{ labels.label_size }}</label>
            <input
              class="form-control col-2 offset-1"
              :name="name + '[width]'"
              type="number"
              :value="widthData"
              :readonly="readonly"
            />
            ×
            <label for="embed-height-size" class="sr-only">{{
              labels.label_height
            }}</label>
            <input
              class="form-control col-2"
              :name="name + '[height]'"
              type="number"
              :value="heightData"
              :readonly="readonly"
            />
            <label>{{ labels.label_pixel }}</label>
          </div>
          <div class="form-group">
            <label>{{ labels.label_matched_embed }}</label>
            <input
              class="form-control title"
              :name="name + '[title]'"
              readonly=""
              title="Title"
              type="text"
              :value="titleData"
            />
            <input
              class="form-control author_name"
              :name="name + '[authorname]'"
              readonly=""
              title="Author"
              type="text"
              :value="authornameData"
            />
            <input
              class="author_url"
              :name="name + '[authorurl]'"
              type="hidden"
              :value="authorurlData"
            />
            <input
              class="html"
              :name="name + '[html]'"
              type="hidden"
              :value="htmlData"
            />
            <input
              class="thumbnail_url"
              :name="name + '[thumbnail]'"
              type="hidden"
              :value="thumbnailData"
            />
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
import field from '../mixins/value';

export default {
  name: 'EditorEmbed',
  mixins: [field],
  props: {
    embedapi: String,
    name: String,
    authorurl: String,
    authorname: String,
    height: Number | String, //String if not set
    html: String,
    thumbnail: String,
    title: String,
    url: String,
    width: Number | String, //String if not set
    labels: Object,
    required: Number,
    readonly: Boolean,
    errormessage: String | Boolean, //string if errormessage is set, and false otherwise
  },
  data: () => {
    return {
      authorurlData: null,
      authornameData: null,
      heightData: null,
      htmlData: null,
      thumbnailData: null,
      titleData: null,
      urlData: null,
      widthData: null,
    };
  },
  watch: {
    url: function(newValue) {
      if (!newValue) {
        return;
      }
      this.debouncedFetchEmbed();
    },
  },
  mounted() {
    this.previewImage = this.thumbnail;
  },
  created: function() {
    this.debouncedFetchEmbed = _.debounce(this.fetchEmbed, 500);
    this.previewImage = this.thumbnail;
  },
  updated() {
    baguetteBox.run('.editor__image--preview', {
      afterShow: () => {
        // noScroll.on();
      },
      afterHide: () => {
        // noScroll.off();
      },
    });
  },
  methods: {
    fetchEmbed: function() {
      const body = new FormData();
      body.append('url', this.url);
      body.append(
        '_csrf_token',
        document.getElementsByName('_csrf_token')[0].value,
      );

      fetch(this.embedapi, { method: 'POST', body: body })
        .then(response => response.json())
        .then(json => {
          this.authorurlData = json.author_url;
          this.authornameData = json.author_name;
          this.heightData = json.height;
          this.htmlData = json.html;
          this.thumbnailData = json.thumbnail_url;
          this.titleData = json.title;
          //this.url          = json.url;
          this.widthData = json.width;
          this.previewImage = json.thumbnail_url;
        })
        .catch(err => {
          console.log(err);
        });
    },
  },
};
</script>
