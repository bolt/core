<template>
    <div class="editor__image" @dragenter="onDragEnter" @dragleave="onDragLeave" @dragover.prevent @drop="onDrop">
        <transition name="fade">
            <div v-show="isDragging" class="editor__image--dragging">
                <i class="fas fa-upload"></i>
            </div>
        </transition>
        <div class="row">
            <div class="col-9">
                <div class="input-group mb-3">
                    <input :name="name + '[media]'" type="hidden" :value="media" />
                    <input
                        class="form-control"
                        :name="name + '[filename]'"
                        type="text"
                        :placeholder="labels.placeholder_filename"
                        :value="filenameData"
                        data-readonly="readonly"
                        :required="required"
                        :data-errormessage="errormessage"
                    />
                </div>
                <div v-if="includeAlt" class="input-group mb-3">
                    <input
                        v-model="altData"
                        class="form-control"
                        :name="name + '[alt]'"
                        type="text"
                        :readonly="readonly"
                        :pattern="pattern"
                        :placeholder="getPlaceholder"
                    />
                </div>
                <div class="btn-toolbar" role="toolbar">
                    <div class="btn-group mr-2" role="group">
                        <button
                            class="btn btn-sm btn-tertiary"
                            type="button"
                            :disabled="readonly"
                            @click="selectUploadFile"
                        >
                            <i class="fas fa-fw fa-upload"></i>{{ labels.button_upload }}
                        </button>

                        <button
                            class="btn btn-sm btn-tertiary dropdown-toggle dropdown-toggle-split"
                            aria-expanded="false"
                            aria-haspopup="true"
                            data-toggle="dropdown"
                            name="image-upload-dropdown"
                            type="button"
                            :disabled="readonly"
                        ></button>

                        <div class="dropdown-menu">
                            <button
                                class="btn dropdown-item"
                                type="button"
                                :disabled="readonly"
                                data-patience="virtue"
                                @click="selectServerFile"
                            >
                                <i class="fas fa-fw fa-th"></i>
                                {{ labels.button_from_library }}
                            </button>

                            <button
                                class="btn dropdown-item"
                                type="button"
                                :disabled="readonly"
                                data-patiance="virtue"
                                @click="uploadFileFromUrl"
                            >
                                <i class="fas fa-fw fa-external-link-alt"></i>
                                {{ labels.button_from_url }}
                            </button>
                            <a
                                v-if="filenameData"
                                class="dropdown-item"
                                :href="attributesLink + '?file=' + filenameData"
                                target="_blank"
                            >
                                <i class="fas fa-fw fa-info-circle"></i>
                                {{ labels.button_edit_attributes }}
                                <small class="dim"><i class="fas fa-external-link-square-alt"></i></small>
                            </a>
                        </div>
                    </div>

                    <div class="btn-group mr-2" role="group">
                        <button
                            v-if="inImagelist"
                            class="btn btn-sm btn-tertiary"
                            type="button"
                            :disabled="isFirstInImagelist || readonly"
                            @click="onMoveImageUp"
                        >
                            <i class="fas fa-fw fa-chevron-up"></i>
                            {{ labels.button_move_up }}
                        </button>

                        <button
                            v-if="inImagelist"
                            class="btn btn-sm btn-tertiary"
                            type="button"
                            :disabled="isLastInImagelist || readonly"
                            @click="onMoveImageDown"
                        >
                            <i class="fas fa-fw fa-chevron-down"></i>
                            {{ labels.button_move_down }}
                        </button>

                        <button
                            class="btn btn-sm btn-hidden-danger"
                            type="button"
                            :disabled="readonly"
                            @click="onRemoveImage"
                        >
                            <i class="fas fa-fw fa-trash"></i> {{ labels.button_remove }}
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
            <div class="col-3">
                <div class="editor__image--preview">
                    <a
                        v-if="previewImage !== ''"
                        class="editor__image--preview-image"
                        :href="previewImage"
                        :style="`background-image: url('${thumbnailImage}')`"
                    >
                    </a>
                </div>
            </div>
        </div>
        <input
            ref="selectFile"
            class="editor__image--upload"
            :name="fieldName"
            tabindex="-1"
            type="file"
            :accept="acceptedExtensions"
            @change="uploadFile($event.target.files[0])"
        />
    </div>
</template>

<script>
import noScroll from 'no-scroll';
import baguetteBox from 'baguettebox.js';
import field from '../mixins/value';
import Axios from 'axios';
import bootbox from 'bootbox';

export default {
    name: 'EditorImage',
    mixins: [field],
    props: {
        filename: String,
        name: String,
        required: Boolean,
        readonly: Boolean,
        thumbnail: String,
        alt: String,
        includeAlt: Boolean,
        directory: String,
        directoryurl: String,
        media: Number | String,
        csrfToken: String,
        labels: Object,
        filelist: String,
        extensions: Array,
        attributesLink: String,
        inImagelist: Boolean,
        isFirstInImagelist: Boolean,
        isLastInImagelist: Boolean,
        errormessage: String | Boolean, //string if errormessage is set, and false otherwise
        pattern: String | Boolean,
        placeholder: String | Boolean,
    },
    data() {
        return {
            previewImage: null,
            thumbnailImage: null,
            isDragging: false,
            dragCount: 0,
            progress: 0,
            filenameData: this.filename,
            altData: this.alt,
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
        getPlaceholder() {
            if (this.placeholder) {
                return this.placeholder;
            }

            return this.labels.placeholder_alt_text;
        },
    },
    mounted() {
        this.resetImage();
    },
    updated() {
        this.resetImage();
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
        resetImage() {
            if (!this.filenameData) {
                this.previewImage = null;
                this.thumbnailImage = null;
                return;
            }
            this.previewImage = `/thumbs/1000×1000/` + this.filenameData;
            this.thumbnailImage = `/thumbs/400×300/` + this.filenameData;
        },
        onMoveImageDown() {
            this.$emit('moveImageDown', this);
        },
        onMoveImageUp() {
            this.$emit('moveImageUp', this);
        },
        onRemoveImage() {
            this.filenameData = null;
            this.resetImage();
            // only reset altData if alt should be displayed.
            if (this.includeAlt) this.altData = '';
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
                        name: 'image-selector',
                        inputOptions: this.filterServerFiles(res.data),
                        callback: function(result) {
                            if (result) {
                                thisField.filenameData = result;
                                thisField.thumbnailData = `/thumbs/400×300/${result}`;
                                thisField.previewData = `/thumbs/1000×1000/${result}`;
                            }
                        },
                    });
                    window.$('.bootbox-input').attr('name', 'bootbox-input');
                    window.reEnablePatientButtons();
                })
                .catch(err => {
                    bootbox.alert(err.response.data + '<br>Image did not upload.');
                    console.warn(err);
                    window.reEnablePatientButtons();
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
            const image = e.dataTransfer.files[0];
            return this.uploadFile(image);
        },
        uploadFile(file) {
            const fd = new FormData();
            const config = {
                onUploadProgress: progressEvent => {
                    const percentCompleted = Math.round((progressEvent.loaded * 100) / progressEvent.total);
                    this.progress = percentCompleted;
                },
                headers: {
                    'Content-Type': 'multipart/form-data',
                },
            };
            fd.append('image', file);
            fd.append('_csrf_token', this.token);
            Axios.post(this.directory, fd, config)
                .then(res => {
                    this.filenameData = res.data;
                    this.thumbnailData = `/thumbs/400×300/${res.data}`;
                    this.previewData = `/thumbs/1000×1000/${res.data}`;
                    this.progress = 0;
                })
                .catch(err => {
                    bootbox.alert(err.response.data.error.message);
                    console.warn(err.response.data.error.message);
                    this.progress = 0;
                });
        },
        uploadFileFromUrl() {
            let thisField = this;
            const config = {
                onUploadProgress: progressEvent => {
                    const percentCompleted = Math.round((progressEvent.loaded * 100) / progressEvent.total);
                    this.progress = percentCompleted;
                },
                headers: {
                    'Content-Type': 'multipart/form-data',
                },
            };
            bootbox.prompt({
                title: 'Upload from URL',
                callback: function(url) {
                    if (url) {
                        const fd = new FormData();
                        fd.append('url', url);
                        fd.append('_csrf_token', thisField.token);
                        Axios.post(thisField.directoryurl, fd, config)
                            .then(res => {
                                thisField.filenameData = res.data;
                                thisField.thumbnailData = `/thumbs/400×300/${res.data}`;
                                thisField.previewData = `/thumbs/1000×1000/${res.data}`;
                                thisField.progress = 0;
                            })
                            .catch(err => {
                                bootbox.alert(err.response.data.error.message);
                                console.warn(err.response.data.error.message);
                                thisField.progress = 0;
                            });
                    }
                },
            });
            window.$('.bootbox-input').attr('name', 'bootbox-input');
            window.reEnablePatientButtons();
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
