<template>
    <div class="editor__image" @dragenter="onDragEnter" @dragleave="onDragLeave" @dragover.prevent @drop="onDrop">
        <transition name="fade">
            <div v-show="isDragging" class="editor__image--dragging">
                <i class="fas fa-upload"></i>
            </div>
        </transition>
        <div class="row">
            <div class="col-12 col-md-3 order-md-2">
                <div class="editor__image--preview">
                    <a
                        v-if="thumbnailImage !== null && thumbnailImage !== ''"
                        class="editor__image--preview-image"
                        :href="previewImage"
                        :style="`background-image: url('${thumbnailImage}')`"
                    >
                        <span class="sr-only">{{ labels.image_preview }}</span>
                    </a>
                </div>
            </div>
            <div class="col order-md-1">
                <div class="input-group mb-3">
                    <input :name="name + '[media]'" type="hidden" :value="media" />
                    <input
                        :title="name + ' filename'"
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
                        :title="name + ' alt'"
                        class="form-control"
                        :name="name + '[alt]'"
                        type="text"
                        :readonly="readonly"
                        :pattern="pattern"
                        :placeholder="getPlaceholder"
                    />
                </div>
                <div class="btn-toolbar" role="toolbar">
                    <div class="btn-group me-2" role="group">
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
                            data-bs-toggle="dropdown"
                            name="image-upload-dropdown"
                            type="button"
                            :disabled="readonly"
                        >
                            <span class="sr-only">{{ labels.button_upload_options }}</span>
                        </button>

                        <div class="dropdown-menu">
                            <button
                                class="btn dropdown-item"
                                type="button"
                                :disabled="readonly"
                                data-patience="virtue"
                                data-bs-toggle="modal"
                                data-bs-target="#resourcesModal"
                                :data-modal-title="labels.modal_title_images"
                                :data-initiator="id"
                                @click="selectServerFile($event)"
                            >
                                <i class="fas fa-fw fa-th"></i>
                                {{ labels.button_from_library }}
                            </button>

                            <button
                                class="btn dropdown-item"
                                type="button"
                                :disabled="readonly"
                                data-patiance="virtue"
                                data-bs-toggle="modal"
                                data-bs-target="#resourcesModal"
                                :data-modal-title="labels.modal_title_upload_from_url"
                                @click="uploadFileFromUrl($event)"
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

                    <div class="btn-group me-2" role="group">
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
        </div>
        <input
            :id="fieldId"
            ref="selectFile"
            :title="name + ' filename'"
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
import $ from 'jquery';
import bootbox from 'bootbox';
import { renable } from '../../patience-is-a-virtue';

export default {
    name: 'EditorImage',
    mixins: [field],
    props: {
        filename: String,
        name: String,
        id: String,
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
        fieldId() {
            return this.id;
        },
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
        generateModalContent(inputOptions) {
            let modalContent = '<div class="row row-cols-1 row-cols-md-3 g-2">';
            inputOptions.forEach((element, key) => {
                modalContent += `
                    <div class="col">
                        <div class="card h-100">
                            <img src="/thumbs/140×73×crop/${element.value}" loading="lazy">
                            <div class="card-body px-2">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" value="${element.value}" id="flexCheckDefault-${key}">
                                    <label class="form-check-label d-inline fs-6" for="flexCheckDefault-${key}">
                                        ${element.text}
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                `;
            });
            modalContent += `</div>`;
            return modalContent;
        },
        generateUploadFromURLModalContent() {
            let modalContent = '';
            modalContent += `
                <form>
                    <input class="form-control" autocomplete="off" type="text" name="from-url-input">
                </form>
            `;
            modalContent += `</div>`;
            return modalContent;
        },
        resetModalContent() {
            let defaultContent = `
                <div class="modal-header">
                    <h5 class="modal-title" id="resourcesModalLabel">
                        <div class="spinner-border" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="spinner-border" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button id="modalSave" type="button" class="btn btn-primary" data-bs-dismiss="modal">Save changes</button>
                </div>
            `;
            var resourcesModal = document.getElementById('resourcesModal');
            resourcesModal.querySelector('.modal-content').innerHTML = defaultContent;
        },
        selectServerFile(event) {
            let thisField = this;
            Axios.get(this.filelist)
                .then(res => {
                    let inputOptions = this.filterServerFiles(res.data);

                    var resourcesModal = document.getElementById('resourcesModal');
                    var saveButton = document.getElementById('modalSave');
                    var button = event.target;
                    var title = button.getAttribute('data-modal-title');
                    var modalTitle = resourcesModal.querySelector('.modal-title');
                    var modalBody = resourcesModal.querySelector('.modal-body');
                    var modalBodyContent = this.generateModalContent(inputOptions);
                    modalTitle.innerHTML = title;
                    modalBody.innerHTML = modalBodyContent;

                    saveButton.addEventListener(
                        'click',
                        () => {
                            var selectedImage = modalBody.querySelector('input[type=checkbox]:checked').value;
                            thisField.filenameData = selectedImage;
                            thisField.thumbnailData = `/thumbs/400×300/${selectedImage}`;
                            thisField.previewData = `/thumbs/1000×1000/${selectedImage}`;
                        },
                        { once: true },
                    );

                    resourcesModal.addEventListener(
                        'hidden.bs.modal',
                        () => {
                            // Reset modal body content when the modal is closed
                            this.resetModalContent();
                        },
                        { once: true },
                    );

                    $('.bootbox-input').attr('name', 'bootbox-input');
                    window.reEnablePatientButtons();
                })
                .catch(() => {
                    renable();
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

            var resourcesModal = document.getElementById('resourcesModal');
            var saveButton = document.getElementById('modalSave');
            var button = event.target;
            var title = button.getAttribute('data-modal-title');
            var modalTitle = resourcesModal.querySelector('.modal-title');
            var modalBody = resourcesModal.querySelector('.modal-body');
            var modalBodyContent = this.generateUploadFromURLModalContent();
            modalTitle.innerHTML = title;
            modalBody.innerHTML = modalBodyContent;

            saveButton.addEventListener(
                'click',
                () => {
                    var imageURL = modalBody.querySelector('input[name=from-url-input]').value;
                    if (imageURL) {
                        const fd = new FormData();
                        fd.append('url', imageURL);
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
                { once: true },
            );

            resourcesModal.addEventListener(
                'hidden.bs.modal',
                () => {
                    // Reset modal body content when the modal is closed
                    this.resetModalContent();
                },
                { once: true },
            );

            $('.bootbox-input').attr('name', 'bootbox-input');
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
