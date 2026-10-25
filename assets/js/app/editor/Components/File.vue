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
                        :aria-label="labels.placeholder_filename"
                        :title="name + ' filename'"
                        class="form-control"
                        :name="name + '[filename]'"
                        type="text"
                        :placeholder="labels.placeholder_filename"
                        :value="filenameData"
                        data-readonly="readonly"
                        :required="required"
                        :data-errormessage="typeof errormessage === 'string' ? errormessage : undefined"
                    />
                </div>
                <div class="input-group mb-3">
                    <input
                        v-model="titleData"
                        :aria-label="labels.placeholder_title"
                        :title="name + ' title'"
                        class="form-control"
                        :name="name + '[title]'"
                        type="text"
                        :required="required"
                        :readonly="readonly"
                        :pattern="typeof pattern === 'string' ? pattern : undefined"
                        :placeholder="getPlaceholder || undefined"
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
                            <i class="fas fa-fw fa-trash"></i>
                            {{ labels.button_remove }}
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
            :aria-label="labels.placeholder_filename"
            :title="name + ' filename'"
            class="editor__file--upload"
            :name="fieldName"
            tabindex="-1"
            type="file"
            :accept="acceptedExtensions"
            @change="uploadSelectedFile"
        />
    </div>
</template>

<script setup lang="ts">
import { ref, computed, useTemplateRef } from 'vue';
import Axios from 'axios';
import type { AxiosError, AxiosProgressEvent } from 'axios';
import { createServerFileBrowser, type ServerFile } from '../utils/serverFileBrowser';
import { getUploadErrorMessage, type UploadErrorResponse } from '../utils/upload';

const props = defineProps<{
    name?: string;
    filename?: string;
    // file.html.twig passes `:thumbnail` and `:alt` (and Filelist forwards
    // `:thumbnail`) from the shared media markup. The file field renders neither,
    // but declaring them keeps the contract honest and avoids attribute fallthrough.
    thumbnail?: string;
    alt?: string;
    title?: string;
    directory?: string;
    media?: string;
    csrfToken?: string;
    labels: Record<string, string>;
    filelist?: string;
    extensions: string[];
    inFilelist?: boolean;
    isFirstInFilelist?: boolean;
    isLastInFilelist?: boolean;
    attributesLink?: string;
    required?: boolean;
    readonly?: boolean;
    errormessage?: string | boolean; //string if errormessage is set, and false otherwise
    pattern?: string | boolean;
    placeholder?: string | boolean;
}>();

const emit = defineEmits<{
    (e: 'moveFileDown', payload: { fieldName: string }): void;
    (e: 'moveFileUp', payload: { fieldName: string }): void;
    (e: 'remove', payload: { fieldName: string }): void;
}>();

const isDragging = ref(false);
const dragCount = ref(0);
const progress = ref(0);
const filenameData = ref(props.filename);
const titleData = ref(props.title);

const selectFile = useTemplateRef<HTMLInputElement>('selectFile');

const fieldName = computed(() => props.name + '[]');
const token = computed(() => props.csrfToken);
const acceptedExtensions = computed(() => props.extensions.map(ext => '.' + ext).join());
const getPlaceholder = computed(() => {
    if (typeof props.placeholder === 'string' && props.placeholder) {
        return props.placeholder;
    }

    return props.labels.placeholder_title;
});

const { selectServerFile } = createServerFileBrowser({
    initialFilelist: props.filelist,
    extensions: props.extensions,
    labels: props.labels,
    modalTitlePrefix: 'Select a file',
    generateModalContent,
    onSelect: selectedFile => {
        filenameData.value = selectedFile;
    },
    onOpenError: err => {
        console.warn(err);
    },
    onNavigateError: err => {
        console.warn(err);
    },
});

function onMoveFileDown() {
    emit('moveFileDown', { fieldName: fieldName.value });
}

function onMoveFileUp() {
    emit('moveFileUp', { fieldName: fieldName.value });
}

function onRemoveFile() {
    filenameData.value = '';
    titleData.value = '';
    emit('remove', { fieldName: fieldName.value });
}

function selectUploadFile() {
    selectFile.value?.click();
}

function generateModalContent(inputOptions: ServerFile[]) {
    const firstOption = inputOptions[0];
    if (!firstOption) {
        return '';
    }

    let filePath = '';
    let folderPath = firstOption.value;
    let baseAsyncPath = firstOption.base_url_path ?? '';
    let fileIcons: Record<string, string> = {
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
    let modalContent = '<div class="row row-cols-1 row-cols-sm-2 row-cols-lg-4 g-2">';
    // If we are deep in the directory, add an arrow to navigate back to previous folder
    if (folderPath.includes('/')) {
        let pathChunks = firstOption.value.split('/');
        pathChunks.pop();
        pathChunks.pop();
        filePath = pathChunks.join('/');
        let baseAsyncUrl = `${baseAsyncPath}?location=${filePath}&type=files`;
        if (filePath != '') {
            modalContent += `
                    <div class="col">
                        <div class="card h-100">
                            <a href="${baseAsyncUrl}" class="directory d-flex align-items-center justify-content-center w-100 flex-grow-1 text-decoration-none">
                                <i class="fas fa-solid fa-level-up-alt fa-3x me-0 align-self-center"></i>
                            </a>
                            <div class="card-body px-2 flex-grow-0 border-top border-very-light-border">
                                <div class="form-check ps-0">
                                    <span class="form-check-label d-inline fs-6 fw-normal d-block"">
                                        ../${filePath}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>`;
        }
    }
    inputOptions.forEach((element, key) => {
        let filenameExtension = element.text.split('.').pop()?.toLowerCase() ?? '';
        if (element.group == 'directories') {
            filePath = element.value;
            let baseAsyncUrl = `${baseAsyncPath}?location=${filePath}&type=files`;
            modalContent += `
                        <div class="col">
                            <div class="card h-100">
                                <a href="${baseAsyncUrl}" class="directory d-flex justify-content-center w-100 flex-grow-1 text-decoration-none align-self-center">
                                    <i class="fas fa-solid fa-folder fa-5x me-0 align-self-center"></i>
                                </a>
<!--                                    <i class="fas fa-solid fa-folder fa-5x me-0 align-self-center"></i>-->
                                <div class="card-body px-2 flex-grow-0 border-top border-very-light-border">
                                    <div class="form-check ps-0">
                                        <label class="form-check-label d-inline fs-6 fw-normal d-block" for="flexCheckDefault-${key}">
                                            ${element.text}
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    `;
        } else {
            modalContent += `
                        <div class="col">
                            <div class="card h-100">
                                <i class="d-flex align-items-center justify-content-center w-100 flex-grow-1 text-decoration-none fas fa-solid ${
                                    fileIcons[filenameExtension] ?? 'fa-file'
                                } fa-5x me-0 align-self-center"></i>
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
}

function onDragEnter(e: DragEvent) {
    e.preventDefault();
    dragCount.value++;
    isDragging.value = true;
    return false;
}

function onDragLeave(e: DragEvent) {
    e.preventDefault();
    dragCount.value--;
    if (dragCount.value <= 0) isDragging.value = false;
}

function onDrop(e: DragEvent) {
    e.preventDefault();
    e.stopPropagation();
    isDragging.value = false;
    dragCount.value = 0;
    const file = e.dataTransfer?.files[0];
    if (!file) {
        return;
    }

    return uploadFile(file);
}

function uploadSelectedFile(event: Event) {
    const file = (event.target as HTMLInputElement).files?.[0];
    if (file) {
        uploadFile(file);
    }
}

function uploadFile(file: File) {
    if (!props.directory) {
        return;
    }

    const fd = new FormData();
    const config = {
        onUploadProgress: (progressEvent: AxiosProgressEvent) => {
            const total = progressEvent.total ?? progressEvent.loaded;
            const percentCompleted = total > 0 ? Math.round((progressEvent.loaded * 100) / total) : 0;
            progress.value = percentCompleted;
        },
        headers: {
            'Content-Type': 'multipart/form-data',
        },
    };
    fd.append('file', file);
    fd.append('_csrf_token', token.value ?? '');
    Axios.post<string>(props.directory, fd, config)
        .then(res => {
            filenameData.value = res.data;
            progress.value = 0;
        })
        .catch((err: AxiosError<UploadErrorResponse>) => {
            window.alert(getUploadErrorMessage(err) + '<br>File did not upload.');
            console.warn(err);
            progress.value = 0;
        });
}
</script>
