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
                        v-if="thumbnailImage"
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
                <div v-if="includeAlt" class="input-group mb-3">
                    <div class="col-sm-2">
                        <label :for="name + '-alt'">Alt:</label>
                    </div>
                    <div class="col-sm-10">
                        <input
                            :id="name + '-alt'"
                            v-model="altData"
                            :title="name + ' alt'"
                            class="form-control"
                            :name="name + '[alt]'"
                            type="text"
                            :readonly="readonly"
                            :pattern="typeof pattern === 'string' ? pattern : undefined"
                            :placeholder="getPlaceholder || undefined"
                        />
                    </div>
                </div>
                <div v-for="(extraFieldProps, extraField) in extraFields" :key="extraField" class="input-group mb-3">
                    <div class="col-sm-2">
                        <label :for="name + '-' + extraField">{{ extraFieldProps.label }}:</label>
                    </div>
                    <div class="col-sm-10">
                        <input
                            :id="name + '-' + extraField"
                            v-model="extraDataValues[extraField]"
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
            :id="fieldId"
            ref="selectFile"
            :title="name + ' filename'"
            class="editor__image--upload"
            :name="fieldName"
            tabindex="-1"
            type="file"
            :accept="acceptedExtensions"
            @change="uploadSelectedFile"
        />
    </div>
</template>

<script setup lang="ts">
import { ref, computed, reactive, watch, useTemplateRef } from 'vue';
import noScroll from 'no-scroll';
import baguetteBox from 'baguettebox.js';
import Axios from 'axios';
import type { AxiosError, AxiosProgressEvent } from 'axios';
import { renable } from '../../patience-is-a-virtue';
import { resetModalContent } from '../../modal';
import { createServerFileBrowser, type ServerFile } from '../utils/serverFileBrowser';
import { getUploadErrorMessage, type UploadErrorResponse } from '../utils/upload';

const props = defineProps<{
    filename?: string;
    name?: string;
    id?: string;
    required?: boolean;
    readonly?: boolean;
    thumbnail?: string;
    alt?: string;
    // Passed by imagelist.html.twig (via Imagelist) and simple_image.html.twig,
    // where the image record carries a `title`. Not rendered by the image field
    // itself; declared so it doesn't leak onto the root element as an attribute.
    title?: string;
    includeAlt?: boolean;
    directory?: string;
    directoryurl?: string;
    media?: number | string;
    csrfToken?: string;
    labels: Record<string, string>;
    filelist?: string;
    extensions: string[];
    attributesLink?: string;
    inImagelist?: boolean;
    isFirstInImagelist?: boolean;
    isLastInImagelist?: boolean;
    errormessage?: string | boolean; //string if errormessage is set, and false otherwise
    pattern?: string | boolean;
    placeholder?: string | boolean;
    extraFields?: Record<string, { label: string; placeholder?: string }>;
    extraData?: Record<string, string | number | boolean | Record<string, string> | string[] | undefined> | string[];
}>();

const emit = defineEmits<{
    (e: 'moveImageDown', payload: { fieldName: string }): void;
    (e: 'moveImageUp', payload: { fieldName: string }): void;
    (e: 'remove', payload: { fieldName: string }): void;
}>();

const isDragging = ref(false);
const dragCount = ref(0);
const progress = ref(0);
const filenameData = ref(props.filename ?? '');
const altData = ref(props.alt);
// Extra-field values reach us in two shapes, both driven by the Bolt backend:
// - standalone image field: image.html.twig builds a `{ extraKey: value }` object
//   (see the `attribute(field, extraKey)` loop), keyed only by the extra fields;
// - inside an imagelist: the parent passes the whole image record (ImageField::
//   getValue()), which also carries base keys like filename/media/thumbnail/extra.
// It can even arrive as an indexed array. Normalize to a lookup and keep only the
// declared extra-field keys, so base keys never leak into the submitted values.
const extraDataLookup: Record<string, unknown> = Object.fromEntries(Object.entries(props.extraData ?? {}));
const extraDataValues = reactive<Record<string, string>>(
    Object.fromEntries(Object.keys(props.extraFields ?? {}).map(key => [key, String(extraDataLookup[key] ?? '')])),
);

const selectFile = useTemplateRef<HTMLInputElement>('selectFile');

const fieldId = computed(() => props.id);
const fieldName = computed(() => props.name + '[]');
const token = computed(() => props.csrfToken);
const acceptedExtensions = computed(() => props.extensions.map(ext => '.' + ext).join());
const getPlaceholder = computed(() => {
    if (typeof props.placeholder === 'string' && props.placeholder) {
        return props.placeholder;
    }

    return props.labels.placeholder_alt_text;
});
const thumbs = computed(() =>
    filenameData.value
        ? {
              preview: `/thumbs/1000×1000/` + filenameData.value,
              thumbnail: `/thumbs/400×300/` + filenameData.value,
          }
        : { preview: undefined, thumbnail: undefined },
);
const previewImage = computed(() => thumbs.value.preview);
const thumbnailImage = computed(() => thumbs.value.thumbnail);

const { selectServerFile } = createServerFileBrowser({
    initialFilelist: props.filelist,
    extensions: props.extensions,
    labels: props.labels,
    modalTitlePrefix: 'Select an image',
    generateModalContent,
    onSelect: selectedImage => {
        filenameData.value = selectedImage;
    },
    onOpenError: err => {
        window.alert(err.message + '<br>Image did not upload.');
    },
    onNavigateError: err => {
        window.alert(err.message + '<br>Image did not upload.');
    },
});

watch(previewImage, initPreviewLightbox, { flush: 'post', immediate: true });

function initPreviewLightbox() {
    if (!previewImage.value) {
        return;
    }

    baguetteBox.run('.editor__image--preview', {
        afterShow: () => {
            noScroll.on();
        },
        afterHide: () => {
            noScroll.off();
        },
    });
}

function onMoveImageDown() {
    emit('moveImageDown', { fieldName: fieldName.value });
}

function onMoveImageUp() {
    emit('moveImageUp', { fieldName: fieldName.value });
}

function onRemoveImage() {
    filenameData.value = '';
    // only reset altData if alt should be displayed.
    if (props.includeAlt) altData.value = '';
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
    let modalContent = '<div class="row row-cols-1 row-cols-sm-2 row-cols-lg-4 g-2">';
    // If we are deep in the directory, add an arrow to navigate back to previous folder
    if (folderPath.includes('/')) {
        let pathChunks = firstOption.value.split('/');
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
}

function generateUploadFromURLModalContent() {
    let modalContent = '';
    modalContent += `
                <form>
                    <input class="form-control" autocomplete="off" type="text" name="from-url-input">
                </form>
            `;
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
    const image = e.dataTransfer?.files[0];
    if (!image) {
        return;
    }

    return uploadFile(image);
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
    fd.append('image', file);
    fd.append('_csrf_token', token.value ?? '');
    Axios.post<string>(props.directory, fd, config)
        .then(res => {
            filenameData.value = res.data;
            progress.value = 0;
        })
        .catch((err: AxiosError<UploadErrorResponse>) => {
            const message = getUploadErrorMessage(err);
            window.alert(message);
            console.warn(message);
            progress.value = 0;
        });
}

function uploadFileFromUrl(event: Event) {
    const uploadUrl = props.directoryurl;
    if (!uploadUrl) {
        return;
    }

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

    const resourcesModal = document.getElementById('resourcesModal');
    const saveButton = document.getElementById('modalButtonAccept');
    const button = event.target as HTMLElement;
    const title = button.getAttribute('data-modal-title');
    const modalTitle = resourcesModal?.querySelector<HTMLElement>('.modal-title');
    const modalBody = resourcesModal?.querySelector<HTMLElement>('.modal-body');
    if (!resourcesModal || !saveButton || !modalTitle || !modalBody) {
        return;
    }

    const modalBodyContent = generateUploadFromURLModalContent();
    modalTitle.innerHTML = title ?? '';

    setTimeout(() => {
        modalBody.innerHTML = modalBodyContent;
    }, 1);

    saveButton.addEventListener(
        'click',
        () => {
            const imageURL = (modalBody.querySelector('input[name=from-url-input]') as HTMLInputElement).value;
            if (imageURL) {
                const fd = new FormData();
                fd.append('url', imageURL);
                fd.append('_csrf_token', token.value ?? '');
                Axios.post<string>(uploadUrl, fd, config)
                    .then(res => {
                        filenameData.value = res.data;
                        progress.value = 0;
                    })
                    .catch((err: AxiosError<UploadErrorResponse>) => {
                        const message = getUploadErrorMessage(err);
                        window.alert(message);
                        console.warn(message);
                        progress.value = 0;
                    });
            }
        },
        { once: true },
    );

    resourcesModal.addEventListener(
        'hidden.bs.modal',
        () => {
            // Reset modal body content when the modal is closed
            resetModalContent(props.labels);
        },
        { once: true },
    );

    renable();
}
</script>
