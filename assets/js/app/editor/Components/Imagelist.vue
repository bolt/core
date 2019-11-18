<template>
  <div>
    <div
      v-for="(child, index) in containerImages"
      :key="index"
      class="form-fieldsgroup"
    >
      <editor-image
        v-if="child.hidden !== true"
        :filename="child.filename"
        :thumbnail="child.thumbnail"
        :title="child.title"
        :alt="child.alt"
        :attributes-link="attributesLink"
        :media="child.media"
        :directory="directory"
        :filelist="filelist"
        :csrf-token="csrfToken"
        :labels="labels"
        :in-imagelist="true"
        :name="fieldName(index)"
        :extensions="extensions"
        @remove="onRemoveImage"
        @updated="onUpdateImage"
        @moveImageUp="onMoveImageUp"
        @moveImageDown="onMoveImageDown"
      ></editor-image>
    </div>

    <button class="btn btn-secondary" type="button" @click="addImage">
      Add image here
    </button>
  </div>
</template>

<script>
import Image from './Image';

export default {
  name: 'EditorImage',
  components: { 'editor-image': Image },
  props: [
    'images',
    'directory',
    'name',
    'filelist',
    'csrfToken',
    'labels',
    'extensions',
    'attributesLink',
  ],
  data: function() {
    return {
      containerImages: this.images,
    };
  },
  methods: {
    getActiveImageFields() {
      return this.containerImages.filter(function(image) {
        return image.hidden !== true;
      });
    },
    getFieldNumberFromElement(elem) {
      return elem.fieldName.match(/\d+/)[0];
    },
    onMoveImageUp(elem){
      console.log("Moving up");

      let fieldNumber = this.getFieldNumberFromElement(elem);
      let imageToMoveUp = this.containerImages[fieldNumber];
      let imageToMoveDown = this.containerImages[fieldNumber-1];

      let newContainerImages = Object.assign({}, this.containerImages);

      newContainerImages[fieldNumber] = imageToMoveDown;
      newContainerImages[fieldNumber-1] = imageToMoveUp;

      this.$set(this.containerImages, newContainerImages);

      this.$forceUpdate();

      //this.$set(this.containerImages, fieldNumber, imageToMoveDown);
      //this.$set(this.containerImages, fieldNumber-1, imageToMoveUp);

      /*
      let fieldNumber = this.getFieldNumberFromElement(elem);
      let tmp = this.containerImages[fieldNumber];

      this.containerImages[fieldNumber] = this.containerImages[fieldNumber-1];
      this.containerImages[fieldNumber-1] = tmp;
      //swap(this.containerImages, fieldNumber, fieldNumber-1);

      //this.$set(this.containerImages, fieldNumber-1, tmp);

       */

    },
    onMoveImageDown(elem){
      console.log("Want to move image down");
    },
    onUpdateImage(elem) {
      let fieldNumber = this.getFieldNumberFromElement(elem);
      this.containerImages[fieldNumber] = elem;
    },
    onRemoveImage(elem) {
      let fieldNumber = this.getFieldNumberFromElement(elem);
      let updatedImage = this.containerImages[fieldNumber];
      updatedImage.hidden = true;
      this.$set(this.containerImages, fieldNumber, updatedImage);

      if (this.getActiveImageFields().length === 0) {
        this.addImage();
      }
    },
    fieldName(index) {
      return this.name + '[' + index + ']';
    },
    addImage() {
      let imageField = {
        inImagelist: true,
        directory: this.directory,
        name: this.name,
        filelist: this.filelist,
        csrfToken: this.csrfToken,
        labels: this.labels,
        thumbnail: '',
        extensions: this.extensions,
      };

      this.containerImages.push(imageField);
    },
  },
};
</script>
