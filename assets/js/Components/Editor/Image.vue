<template>
  <div 
    class="form-group field__image"
    @dragenter="onDragEnter"
    @dragleave="onDragLeave"
    @dragover.prevent
    @drop="onDrop"
  >
    <transition name="fade">
      <div class="field__image--dragging" v-show="isDragging">
        <i class="fas fa-upload"></i>
      </div>  
    </transition>
    <div class="row">
      <div class="col-8">
        <label>{{ label }}</label>
        <div class="input-group mb-3">
          <input :name="name + '[alt]'" type="text" class="form-control" placeholder="alt text" :value="alt">
        </div>
        <div class="input-group mb-3">
          <input :name="name + '[title]'" type="text" class="form-control" placeholder="title" :value="title">
        </div>
        <div class="btn-toolbar" role="toolbar" >
          <div class="btn-group mr-2" role="group">
            <button type="button" class="btn btn-secondary" @click="selectFile">
              <i class="fas fa-fw fa-upload"></i> Upload
            </button>
          </div>
          <div class="btn-group mr-2" role="group">
            <button type="button" class="btn btn-secondary">
              <i class="fas fa-fw fa-th"></i> From Library
            </button>
          </div>
        </div>
      </div>
      <div class="col-4">
        <label>Preview:</label>
        <div class="field__image--preview">
          <a 
            :href="previewImage" 
            class="field__image--preview-image"
            :style="`background-image: url('${previewImage}')`"
          >
          </a>
        </div>
      </div>
    </div>
    <input :name="fieldName" type="file" @change="uploadFile($event.target.files[0])" ref="selectFile" class="field__image--upload">
    <input :name="name + '[filename]'" type="hidden" :value="val">
  </div>
</template>

<script>
import noScroll from 'no-scroll';
import baguetteBox from 'baguettebox.js';
import field from '../../helpers/mixins/fieldValue';

export default {
  name: "editor-image",
  props: ['label', 'name', 'value', 'thumbnail', 'alt', 'title', 'directory'],
  mixins: [field],
  mounted(){
    this.previewImage = this.thumbnail;
  },
  updated() {
    baguetteBox.run('.field__image--preview', {
      afterShow: () =>{
        noScroll.on()
      },
      afterHide: () =>{
        noScroll.off()
      }
    });
  },
  data: () => {
    return {
      previewImage: null,
      isDragging: false,
      dragCount: 0,
    };
  },
  methods: {
    selectFile(){
      this.$refs.selectFile.click()
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
        if (this.dragCount <= 0)
        this.isDragging = false;
    },
    onDrop(e) {
        e.preventDefault();
        e.stopPropagation();
        this.isDragging = false;
        const image = e.dataTransfer.files[0];
        return this.uploadFile(image);
    },
    uploadFile(file){
      const thumbnailParams = this.thumbnail.split('?').pop();
      const fd = new FormData();
      fd.append('image', file);
      this.$axios.post(this.directory, fd, {
        headers: {
          'Content-Type': 'multipart/form-data'
        }
      }).then(res => {
        this.val = res.data;
        this.previewImage = `/thumbs/${res.data}?${thumbnailParams}`;
      })
      .catch(err => {
        console.log(err);
      })
    },
  },
  computed:{
    fieldName(){
      return this.name + '[]'
    }
  }
};
</script>
