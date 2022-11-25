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
                    <div class="col-sm-2">
                        <label>Alt:</label>
                    </div>
                    <div class="col-sm-10">
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
                </div>
                <div v-for="(extraFieldProps, extraField) in extraFields" :key="extraField" class="input-group mb-3">
                    <div class="col-sm-2">
                        <label>{{ extraFieldProps.label }}:</label>
                    </div>
                    <div class="col-sm-10">
                        <input
                            v-model="extraData[extraField]"
                            :title="name + ' ' + extraField"
                            class="form-control"
                            :name="name + '[' + extraField + ']'"
                            type="text"
                            :placeholder="extraFieldProps.placeholder"
                        />
                    </div>
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
                                data-modal-dialog-class="modal-xl"
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
import { Modal } from 'bootstrap';
import { renable } from '../../patience-is-a-virtue';
import { resetModalContent } from '../../modal';

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
        extraFields: Array,
        extraData: Array,
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
            let filePath = '';
            let folderPath = inputOptions[0].value;
            let baseAsyncPath = inputOptions[0].base_url_path;
            let modalContent = '<div class="row row-cols-1 row-cols-sm-2 row-cols-lg-4 g-2">';
            // If we are deep in the directory, add an arrow to navigate back to previous folder
            if (folderPath.includes('/')) {
                let pathChunks = inputOptions[0].value.split('/');
                pathChunks.pop();
                pathChunks.pop();
                filePath = pathChunks.join('/');
                let baseAsyncUrl = `${baseAsyncPath}?location=${filePath}&type=images`;

                if (filePath != '') {
                    modalContent += `
                    <div class="col">
                        <div class="card h-100">
                            <a href="${baseAsyncUrl}" class="directory d-flex justify-content-center w-100 flex-grow-1 text-decoration-none align-self-center">
                                <i class="fas fa-solid fa-level-up-alt fa-3x me-0 align-self-center"></i>
                            </a>
                            <div class="card-body px-2 flex-grow-0 border-top border-very-light-border">
                                <div class="form-check ps-0">
                                    <span class="form-check-label d-inline fs-6 fw-normal d-block">
                                        ../${filePath}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>`;
                }
            }
            inputOptions.forEach((element, key) => {
                if (element.group == 'directories') {
                    filePath = element.value;
                    let baseAsyncUrl = `${baseAsyncPath}?location=${filePath}&type=images`;
                    // let directoryPath = '/bolt/async/list_files?location=files/' + element.value + '&type=images';
                    modalContent += `
                    <div class="col">
                        <div class="card">
                            <a href="${baseAsyncUrl}" class="directory d-flex justify-content-center w-100 flex-grow-1 text-decoration-none align-self-center">
                                <i class="fas fa-solid fa-folder fa-5x me-0 align-self-center"></i>
                            </a>
                            <div class="card-body px-2 flex-grow-0 border-top border-very-light-border">
                                <div class="form-check ps-0">
                                    <span class="form-check-label d-inline fs-6 fw-normal d-block">
                                        /${element.text}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>`;
                } else {
                    modalContent += `
                    <div class="col">
                        <div class="card">
                            <img src="/thumbs/250×150×crop/${element.value.replace('files/', '')}" loading="lazy">
                            <div class="card-body px-2 flex-grow-0 border-top border-very-light-border">
                                <div class="form-check ps-0">
                                    <input class="form-check-input" type="checkbox" value="${
                                        element.value
                                    }" id="flexCheckDefault-${key}">
                                    <label class="form-check-label d-inline fs-6 fw-normal d-block" for="flexCheckDefault-${key}">
                                        ${element.text}
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                    `;
                }
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
            return modalContent;
        },
        selectServerFile(event) {
            let thisField = this;
            Axios.get(this.filelist)
                .then(res => {
                    let inputOptions = this.filterServerFiles(res.data);

                    var resourcesModal = document.getElementById('resourcesModal');
                    var bootstrapResourcesModal = document.querySelector('#resourcesModal');
                    var resourcesModalObject = Modal.getOrCreateInstance(bootstrapResourcesModal); // Returns a Bootstrap modal instance
                    var button = event.target;
                    var title = button.getAttribute('data-modal-title');
                    var modalDialog = resourcesModal.querySelector('.modal-dialog');
                    var modalTitle = resourcesModal.querySelector('.modal-title');
                    var modalBody = resourcesModal.querySelector('.modal-body');
                    var modalFooter = resourcesModal.querySelector('.modal-footer');
                    var modalBodyContent = this.generateModalContent(inputOptions);

                    modalDialog.classList.add(button.getAttribute('data-modal-dialog-class'));
                    modalTitle.innerHTML = title;
                    modalBody.innerHTML = modalBodyContent;
                    modalFooter.remove();

                    var directoryLinks = resourcesModal.querySelectorAll('.directory');

                    directoryLinks.forEach(link => {
                        link.addEventListener('click', e => {
                            e.preventDefault();
                            this.filelist = link.href;
                            thisField.filelist = link.href;
                            this.navigateDirectory();
                            // return false;
                        });
                    });

                    var cards = modalBody.querySelectorAll('.form-check-input');
                    cards.forEach(card => {
                        card.addEventListener('click', () => {
                            resourcesModalObject.hide();
                        });
                    });

                    resourcesModal.addEventListener(
                        'hidden.bs.modal',
                        () => {
                            if (modalBody.querySelector('input[type=checkbox]:checked')) {
                                var selectedImage = modalBody.querySelector('input[type=checkbox]:checked').value;
                                thisField.filenameData = selectedImage.replace('files/', '');
                                thisField.thumbnailData = `/thumbs/400×300/${selectedImage.replace('files/', '')}`;
                                thisField.previewData = `/thumbs/1000×1000/${selectedImage.replace('files/', '')}`;
                            }
                            // Reset modal body content when the modal is closed
                            resetModalContent(this.labels);
                        },
                        { once: true },
                    );

                    renable();
                })
                .catch(err => {
                    window.alert(err.response.data + '<br>Image did not upload.');
                    renable();
                });
        },
        navigateDirectory() {
            let thisField = this;
            Axios.get(this.filelist)
                .then(res => {
                    let inputOptions = this.filterServerFiles(res.data);
                    let folderPath = '';

                    // Generate current folder path to add to modal title
                    folderPath = inputOptions[0].value.split('/');
                    folderPath.pop();
                    folderPath = folderPath.join('/');

                    var resourcesModal = document.getElementById('resourcesModal');
                    var bootstrapResourcesModal = document.querySelector('#resourcesModal');
                    var resourcesModalObject = Modal.getOrCreateInstance(bootstrapResourcesModal); // Returns a Bootstrap modal instance
                    var modalTitle = resourcesModal.querySelector('.modal-title');
                    var modalBody = resourcesModal.querySelector('.modal-body');
                    var modalBodyContent = this.generateModalContent(inputOptions);

                    modalTitle.innerHTML = 'Select an image: <i class="fas fa-solid fa-folder-tree"></i>' + folderPath;
                    modalBody.innerHTML = modalBodyContent;

                    var directoryLinks = resourcesModal.querySelectorAll('.directory');

                    directoryLinks.forEach(link => {
                        link.addEventListener('click', e => {
                            e.preventDefault();
                            this.filelist = link.href;
                            thisField.filelist = link.href;
                            this.navigateDirectory(e);
                        });
                    });

                    var cards = modalBody.querySelectorAll('.form-check-input');
                    cards.forEach(card => {
                        card.addEventListener('click', () => {
                            resourcesModalObject.hide();
                        });
                    });
                })
                .catch(err => {
                    window.alert(err.response.data + '<br>Image did not upload.');
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
                    window.alert(err.response.data.error.message);
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
            var saveButton = document.getElementById('modalButtonAccept');
            var button = event.target;
            var title = button.getAttribute('data-modal-title');
            var modalTitle = resourcesModal.querySelector('.modal-title');
            var modalBody = resourcesModal.querySelector('.modal-body');
            var modalBodyContent = this.generateUploadFromURLModalContent();
            modalTitle.innerHTML = title;

            setTimeout(() => {
                modalBody.innerHTML = modalBodyContent;
            }, 1);

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
                                window.alert(err.response.data.error.message);
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
                    resetModalContent(this.labels);
                },
                { once: true },
            );

            renable();
        },
        filterServerFiles(files) {
            let self = this;
            return files.filter(function(file) {
                let ext = /(?:\.([^.]+))?$/.exec(file.text)[1];
                // If it's a directory, return the directory
                if (file.group == 'directories') {
                    return file;
                }
                return self.extensions.includes(ext);
            });
        },
    },
};
</script>
