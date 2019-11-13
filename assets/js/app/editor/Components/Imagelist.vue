<template>
    <div>
       <div v-for="(child, index) in images" v-bind:key="index">
            <editor-image
                          :filename=child.filename
                          :thumbnail=child.thumbnail
                          :title=child.title
                          :alt=child.alt
                          :media=child.media
                          :directory=directory
                          :filelist=filelist
                          :csrf-token=csrfToken
                          :labels=labels
                          :removable=true
                          :name="fieldName(index)"
                          :extensions=extensions
                          @clicked="onRemoveImage"
            ></editor-image>
        </div>

        <button
                class="btn btn-secondary"
                type="button"
                @click="addImage"
        >Add image here
        </button>
    </div>
</template>

<script>
    import Image from './Image';

    export default {
        name: 'EditorImage',
        //mixins: [field],
        props: [
            'images',
            'directory',
            'name',
            'filelist',
            'csrfToken',
            'labels',
            'extensions'
        ],
        methods: {
            onRemoveImage(elem){
                this.images = this.images.filter(function(image){
                    let fieldNumber = elem.fieldName.match(/\d+/)[0];
                    return image.fieldname !== fieldNumber;
                });
                this.$forceUpdate();
            },
            fieldName(index){
                return this.name + "[" + index + "]";
            },
            addImage(){
                let imageField = {
                    removable: true,
                    directory: this.directory,
                    name: this.name,
                    filelist: this.filelist,
                    csrfToken: this.csrfToken,
                    labels: this.labels,
                    thumbnail: "",
                    extensions: this.extensions,
                };
                this.images.push(imageField);
                this.$forceUpdate();
            },

        },
        components: { "editor-image": Image}
    };
</script>
