<template>
  <div
    class="editor__image"
    @dragenter="onDragEnter"
    @dragleave="onDragLeave"
    @dragover.prevent
    @drop="onDrop"
  >
    <transition name="fade">
      <div v-show="isDragging" class="editor__image--dragging">
        <i class="fas fa-upload"></i>
      </div>
    </transition>
    <div class="row">
      <div class="col-8">
        <div class="input-group mb-3">
          <input :name="name + '[media]'" type="hidden" :value="media" />
          <input
            :name="name + '[filename]'"
            type="text"
            class="form-control"
            placeholder="filename"
            :value="filename"
          />
        </div>
        <div class="input-group mb-3">
          <input
            :name="name + '[alt]'"
            type="text"
            class="form-control"
            placeholder="alt text"
            :value="alt"
          />
        </div>
        <div class="input-group mb-3">
          <input
            :name="name + '[title]'"
            type="text"
            class="form-control"
            placeholder="title"
            :value="title"
          />
        </div>
        <div class="btn-toolbar" role="toolbar">
          <div class="btn-group mr-2" role="group">
            <button type="button" class="btn btn-secondary" @click="selectFile">
              <i class="fas fa-fw fa-upload"></i>{{ labels.button_upload }}
            </button>
          </div>
          <div class="btn-group mr-2" role="group">
            <button type="button" class="btn btn-secondary">
              <i class="fas fa-fw fa-th"></i> {{ labels.button_from_library }}
            </button>
          </div>
        </div>
        <div v-if="progress > 0" class="progress mt-3">
          <div
            class="progress-bar progress-bar-striped progress-bar-animated"
            role="progressbar"
            :aria-valuenow="progress"
            aria-valuemin="0"
            aria-valuemax="100"
            :style="`width: ${progress}%`"
          ></div>
        </div>
      </div>
      <div class="col-4">
        <div class="editor__image--preview">
          <a
            :href="previewImage"
            class="editor__image--preview-image"
            :style="`background-image: url('${previewImage}')`"
          >
          </a>
        </div>
      </div>
    </div>
    <input
      ref="selectFile"
      :name="fieldName"
      type="file"
      class="editor__image--upload"
      @change="uploadFile($event.target.files[0])"
    />
  </div>
</template>

<script>
import noScroll from 'no-scroll';
import baguetteBox from 'baguettebox.js';
import field from '../../../mixins/value';
import Axios from 'axios';

export default {
  name: 'EditorImage',
  mixins: [field],
  props: [
    'label',
    'filename',
    'name',
    'thumbnail',
    'alt',
    'title',
    'directory',
    'media',
    'csrf_token',
    'labels',
  ],
  data: () => {
    return {
      previewImage: null,
      isDragging: false,
      dragCount: 0,
      progress: 0,
    };
  },
  computed: {
    fieldName() {
      return this.name + '[]';
    },
    token() {
      return this.csrf_token;
    }
  },
  mounted() {
    this.previewImage = this.thumbnail;
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
  methods: {
    selectFile() {
      this.$refs.selectFile.click();
    },
    onDragEnter(e) {
      e.preventDefault();
      this.dragCount++;
      this.isDragging = true;
      return false;
    },
    onDragLeave(e) {
      e.preventDefault();
      this.dragCount--;
      if (this.dragCount <= 0) this.isDragging = false;
    },
    onDrop(e) {
      e.preventDefault();
      e.stopPropagation();
      this.isDragging = false;
      const image = e.dataTransfer.files[0];
      return this.uploadFile(image);
    },
    uploadFile(file) {
      const thumbnailParams = this.thumbnail.split('?').pop();
      const fd = new FormData();
      const config = {
        onUploadProgress: progressEvent => {
          const percentCompleted = Math.round(
            (progressEvent.loaded * 100) / progressEvent.total,
          );
          this.progress = percentCompleted;
        },
        headers: {
          'Content-Type': 'multipart/form-data',
        },
      };
      fd.append('image', file);
      fd.append('_csrf_token', this.token);
      Axios
        .post(this.directory, fd, config)
        .then(res => {
          this.filename = res.data;
          this.previewImage = `/thumbs/${res.data}?${thumbnailParams}`;
          this.progress = 0;
        })
        .catch(err => {
          console.warn(err);
          this.progress = 0;
        });
    },
  },
};
</script>
