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
                            name="file-upload-dropdown"
                            type="button"
                            :disabled="readonly"
                        ><span class="sr-only">{{ labels.button_upload_options }}</span></button>

                        <div class="dropdown-menu">
                            <button
                                class="btn dropdown-item"
                                type="button"
                                :readonly="readonly"
                                data-patience="virtue"
                                @click="selectServerFile"
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

                    <div class="btn-group mr-2" role="group">
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
import bootbox from 'bootbox';

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
                    window.$('.bootbox-input').attr('name', 'bootbox-input');
                    window.reEnablePatientButtons();
                })
                .catch(err => {
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
                    bootbox.alert(errorMessage + '<br>File did not upload.');
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
