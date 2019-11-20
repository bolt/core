<template>
  <div
    class="editor__file"
    @dragenter="onDragEnter"
    @dragleave="onDragLeave"
    @dragover.prevent
    @drop="onDrop"
  >
    <transition name="fade">
      <div v-show="isDragging" class="editor__file--dragging">
        <i class="fas fa-upload"></i>
      </div>
    </transition>
    <div class="row">
      <div class="col-8">
        <div class="input-group mb-3">
          <input :name="name + '[media]'" type="hidden" :value="media" />
          <input
            class="form-control"
            :name="name + '[filename]'"
            type="text"
            :placeholder="labels.placeholder_filename"
            :value="filenameData"
          />
        </div>
        <div class="input-group mb-3">
          <input
            v-model="titleData"
            class="form-control"
            :name="name + '[title]'"
            type="text"
            :placeholder="labels.placeholder_title"

          />
        </div>
        <div class="btn-toolbar" role="toolbar">
          <div class="btn-group mr-2" role="group">
            <button
              class="btn btn-secondary"
              type="button"
              @click="selectUploadFile"
            >
              <i class="fas fa-fw fa-upload"></i>{{ labels.button_upload }}
            </button>
          </div>
          <div class="btn-group mr-2" role="group">
            <button
              class="btn btn-secondary"
              type="button"
              @click="selectServerFile"
            >
              <i class="fas fa-fw fa-th"></i> {{ labels.button_from_library }}
            </button>
          </div>

          <div v-if="inFilelist == true" class="btn-group mr-2" role="group">
            <button
                    class="btn btn-secondary"
                    type="button"
                    :disabled="isFirstInFilelist"
                    @click="onMoveFileUp"
            >
              <i class="fas fa-fw fa-chevron-up"></i>
              {{ labels.button_move_up }}
            </button>
          </div>
          <div v-if="inFilelist == true" class="btn-group mr-2" role="group">
            <button
                    class="btn btn-secondary"
                    type="button"
                    :disabled="isLastInFilelist"
                    @click="onMoveFileDown"
            >
              <i class="fas fa-fw fa-chevron-down"></i>
              {{ labels.button_move_down }}
            </button>
          </div>
          <div v-if="inFilelist == true" class="btn-group mr-2" role="group">
            <button
                    class="btn btn-hidden-danger"
                    type="button"
                    @click="onRemoveFile"
            >
              <i class="fas fa-fw fa-times"></i> {{ labels.button_remove }}
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
    </div>
    <input
      ref="selectFile"
      class="editor__file--upload"
      :name="fieldName"
      type="file"
      :accept="acceptedExtensions"
      @change="uploadFile($event.target.files[0])"
    />
  </div>
</template>

<script>
import field from '../mixins/value';
import Axios from 'axios';
import bootbox from 'bootbox';

export default {
  name: 'EditorFile',
  mixins: [field],
  props: [
    'label',
    'filename',
    'name',
    'title',
    'directory',
    'media',
    'csrfToken',
    'labels',
    'filelist',
    'extensions',
    'inFilelist',
    'isFirstInFilelist',
    'isLastInFilelist',
  ],
  data(){
    return {
      isDragging: false,
      dragCount: 0,
      progress: 0,
      filenameData: this.filename,
      titleData: this.title,
    };
  },
  computed: {
    fieldName() {
      return this.name + '[]';
    },
    token() {
      return this.csrfToken;
    },
    acceptedExtensions() {
      return this.extensions.map(ext => '.' + ext).join();
    },
  },
  methods: {
    onMoveFileDown() {
      this.$emit('moveFileDown', this);
    },
    onMoveFileUp() {
      this.$emit('moveFileUp', this);
    },
    onRemoveFile() {
      this.$emit('remove', this);
    },
    selectUploadFile() {
      this.$refs.selectFile.click();
    },
    selectServerFile() {
      let thisField = this;
      Axios.get(this.filelist)
        .then(res => {
          bootbox.prompt({
            title: 'Select a file',
            inputType: 'select',
            inputOptions: this.filterServerFiles(res.data),
            callback: function(result) {
              if (result) {
                thisField.filenameData = result;
              }
            },
          });
        })
        .catch(err => {
          console.warn(err);
        });
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
      this.dragCount = 0;
      const file = e.dataTransfer.files[0];
      return this.uploadFile(file);
    },
    uploadFile(file) {
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
      fd.append('file', file);
      fd.append('_csrf_token', this.token);
      Axios.post(this.directory, fd, config)
        .then(res => {
          this.filenameData = res.data;
          this.progress = 0;
        })
        .catch(err => {
          console.warn(err);
          this.progress = 0;
        });
    },
    filterServerFiles(files) {
      let self = this;
      return files.filter(function(file) {
        let ext = /(?:\.([^.]+))?$/.exec(file.text)[1];
        return self.extensions.includes(ext);
      });
    },
  },
};
</script>
