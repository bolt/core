<template>
    <div class="editor__file" @dragenter="onDragEnter" @dragleave="onDragLeave" @dragover.prevent @drop="onDrop">
        <transition name="fade">
            <div v-show="isDragging" class="editor__file--dragging">
                <i class="fas fa-upload"></i>
            </div>
        </transition>
        <div class="row">
            <div class="col-12">
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
                <div class="input-group mb-3">
                    <input
                        v-model="titleData"
                        :title="name + ' title'"
                        class="form-control"
                        :name="name + '[title]'"
                        type="text"
                        :required="required"
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
                            name="file-upload-dropdown"
                            type="button"
                            :disabled="readonly"
                        >
                            <span class="sr-only">{{ labels.button_upload_options }}</span>
                        </button>

                        <div class="dropdown-menu">
                            <button
                                class="btn dropdown-item"
                                type="button"
                                :readonly="readonly"
                                data-patience="virtue"
                                data-bs-toggle="modal"
                                data-bs-target="#resourcesModal"
                                :data-modal-title="labels.modal_title_files"
                                data-modal-dialog-class="modal-xl"
                                @click="selectServerFile($event)"
                            >
                                <i class="fas fa-fw fa-th"></i>
                                {{ labels.button_from_library }}
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
                            v-if="inFilelist == true"
                            class="btn btn-sm btn-tertiary"
                            type="button"
                            :disabled="isFirstInFilelist || readonly"
                            @click="onMoveFileUp"
                        >
                            <i class="fas fa-fw fa-chevron-up"></i>
                            {{ labels.button_move_up }}
                        </button>

                        <button
                            v-if="inFilelist == true"
                            class="btn btn-sm btn-tertiary"
                            type="button"
                            :disabled="isLastInFilelist || readonly"
                            @click="onMoveFileDown"
                        >
                            <i class="fas fa-fw fa-chevron-down"></i>
                            {{ labels.button_move_down }}
                        </button>

                        <button
                            class="btn btn-sm btn-hidden-danger"
                            type="button"
                            :disabled="readonly"
                            @click="onRemoveFile"
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
            ref="selectFile"
            :title="name + ' filename'"
            class="editor__file--upload"
            :name="fieldName"
            tabindex="-1"
            type="file"
            :accept="acceptedExtensions"
            @change="uploadFile($event.target.files[0])"
        />
    </div>
</template>

<script>
import field from '../mixins/value';
import Axios from 'axios';
import { renable } from '../../patience-is-a-virtue';
import $ from 'jquery';
import { resetModalContent } from '../../modal';

export default {
    name: 'EditorFile',
    mixins: [field],
    props: {
        name: String,
        filename: String,
        title: String,
        directory: String,
        media: String,
        csrfToken: String,
        labels: Object,
        filelist: String,
        extensions: Array,
        inFilelist: Boolean,
        isFirstInFilelist: Boolean,
        isLastInFilelist: Boolean,
        attributesLink: String,
        required: Boolean,
        readonly: Boolean,
        errormessage: String | Boolean, //string if errormessage is set, and false otherwise
        pattern: String | Boolean,
        placeholder: String | Boolean,
    },
    data() {
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
        getPlaceholder() {
            if (this.placeholder) {
                return this.placeholder;
            }

            return this.labels.placeholder_title;
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
            this.filenameData = '';
            this.titleData = '';
            this.$emit('remove', this);
        },
        selectUploadFile() {
            this.$refs.selectFile.click();
        },
        generateModalContent(inputOptions) {
            let fileIcons = {
                jpg: 'fa-file-image',
                jpeg: 'fa-file-image',
                png: 'fa-file-image',
                webp: 'fa-file-image',
                svg: 'fa-file-image',
                gif: 'fa-file-image',
                pdf: 'fa-file-pdf',
                doc: 'fa-file-word',
                docx: 'fa-file-word',
                txt: 'fa-file-alt',
                csv: 'fa-file-csv',
                xls: 'fa-file-excel',
                xlsx: 'fa-file-excel',
                pptx: 'fa-file-powerpoint',
                html: 'fa-file-code',
                mp3: 'fa-music',
                mp4: 'fa-video',
                mov: 'fa-video',
                avi: 'fa-video',
                webm: 'fa-video',
                zip: 'fa-file-archive',
                rar: 'fa-file-archive',
                gz: 'fa-file-archive',
            };
            let modalContent = '<div class="row row-cols-1 row-cols-sm-2 row-cols-lg-3 g-2">';
            inputOptions.forEach((element, key) => {
                let filenameExtension = element.text
                    .split('.')
                    .pop()
                    .toLowerCase();
                modalContent += `
                    <div class="col">
                        <div class="card h-100 pt-3">
                            <i class="fas fa-solid ${fileIcons[filenameExtension] ??
                                'fa-file'} fa-5x me-0 align-self-center"></i>
                            <div class="card-body px-2">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" value="${
                                        element.value
                                    }" id="flexCheckDefault-${key}">
                                    <label class="form-check-label d-inline fs-6 fw-normal" for="flexCheckDefault-${key}">
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
        selectServerFile(event) {
            let thisField = this;
            Axios.get(this.filelist)
                .then(res => {
                    let inputOptions = this.filterServerFiles(res.data);

                    var resourcesModal = document.getElementById('resourcesModal');
                    var saveButton = document.getElementById('modalButtonAccept');
                    var button = event.target;
                    var title = button.getAttribute('data-modal-title');
                    var modalTitle = resourcesModal.querySelector('.modal-title');
                    var modalBody = resourcesModal.querySelector('.modal-body');
                    var modalBodyContent = this.generateModalContent(inputOptions);
                    
                    modalDialog.classList.add(button.getAttribute('data-modal-dialog-class'));
                    modalTitle.innerHTML = title;
                    modalBody.innerHTML = modalBodyContent;

                    saveButton.addEventListener(
                        'click',
                        () => {
                            if (modalBody.querySelector('input[type=checkbox]:checked')) {
                                var selectedImage = modalBody.querySelector('input[type=checkbox]:checked').value;
                                thisField.filenameData = selectedImage;
                            }
                        },
                        { once: true },
                    );

                    resourcesModal.addEventListener(
                        'hidden.bs.modal',
                        () => {
                            // Reset modal body content when the modal is closed
                            resetModalContent(this.labels);
                        },
                        { once: true },
                    );

                    $('.bootbox-input').attr('name', 'bootbox-input');
                    renable();
                })
                .catch(err => {
                    console.warn(err);
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
            const file = e.dataTransfer.files[0];
            return this.uploadFile(file);
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
            fd.append('file', file);
            fd.append('_csrf_token', this.token);
            Axios.post(this.directory, fd, config)
                .then(res => {
                    this.filenameData = res.data;
                    this.progress = 0;
                })
                .catch(err => {
                    const responseData = err.response.data;
                    let errorMessage = 'unknown error';
                    if (typeof responseData === 'string' || responseData instanceof String) {
                        errorMessage = responseData;
                    } else if (responseData.error && responseData.error.message) {
                        errorMessage = responseData.error.message;
                    }
                    window.alert(errorMessage + '<br>File did not upload.');
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
