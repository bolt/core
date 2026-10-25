<template>
    <div class="editor-filelist">
        <div v-for="(child, index) in containerFiles" :key="child.id" class="form-fieldsgroup">
            <editor-file
                :filename="child.filename"
                :thumbnail="child.thumbnail"
                :title="child.title"
                :attributes-link="attributesLink"
                :media="child.media"
                :directory="directory"
                :filelist="filelist"
                :csrf-token="csrfToken"
                :labels="labelsValue"
                :in-filelist="true"
                :name="fieldName(index)"
                :extensions="extensionsValue"
                :is-first-in-filelist="isFirstInFilelist(index)"
                :is-last-in-filelist="isLastInFilelist(index)"
                :readonly="readonly"
                @remove="onRemoveFile"
                @move-file-up="onMoveFileUp"
                @move-file-down="onMoveFileDown"
            ></editor-file>
        </div>
        <div v-if="containerFiles.length === 0">
            <input :name="name" value="" type="hidden" />
        </div>

        <button class="btn btn-tertiary" type="button" :disabled="!allowMore" @click="addFile">
            <i class="fas fa-fw fa-plus"></i>
            {{ labelsValue.add_new_file }}
        </button>
    </div>
</template>

<script setup lang="ts">
import { ref, computed } from 'vue';
import EditorFile from './File.vue';
import type { FieldMovePayload } from '../types';

type FileField = {
    id: number;
    filename?: string;
    thumbnail?: string;
    title?: string;
    media?: string;
    inFilelist?: boolean;
    directory?: string;
    name?: string;
    filelist?: string;
    csrfToken?: string;
    labels?: Record<string, string>;
    extensions?: string[];
};

const props = defineProps<{
    files?: Omit<FileField, 'id'>[];
    directory?: string;
    name?: string;
    filelist?: string;
    csrfToken?: string;
    labels?: Record<string, string>;
    extensions?: string[];
    attributesLink?: string;
    limit?: number;
    readonly?: boolean;
}>();

const counter = ref(0);
const containerFiles = ref<FileField[]>([]);
const labelsValue = computed(() => props.labels ?? {});
const extensionsValue = computed(() => props.extensions ?? []);

const initialFiles = props.files || [];
initialFiles.forEach((file, index) => {
    containerFiles.value.push({ ...file, id: index });
    counter.value++;
});

const allowMore = computed(() => {
    if (props.readonly) {
        return false;
    }
    return containerFiles.value.length < (props.limit ?? Infinity);
});

function isFirstInFilelist(index: number) {
    return index === 0;
}

function isLastInFilelist(index: number) {
    return index === containerFiles.value.length - 1;
}

function getFieldNumberFromElement(elem: FieldMovePayload) {
    // get the last number because in collections, there are multiple.
    const matches = [...elem.fieldName.matchAll(/\d+/g)];
    const lastMatch = matches.splice(-1).pop();
    return lastMatch ? parseInt(lastMatch[0]) : 0;
}

function onMoveFileDown(elem: FieldMovePayload) {
    const fieldNumber = getFieldNumberFromElement(elem);

    if (fieldNumber < containerFiles.value.length - 1) {
        const fileToMoveDown = containerFiles.value[fieldNumber];
        const fileToMoveUp = containerFiles.value[fieldNumber + 1];

        containerFiles.value.splice(fieldNumber, 2, fileToMoveUp, fileToMoveDown);
    }
}

function onMoveFileUp(elem: FieldMovePayload) {
    const fieldNumber = getFieldNumberFromElement(elem);

    if (fieldNumber > 0) {
        const fileToMoveUp = containerFiles.value[fieldNumber];
        const fileToMoveDown = containerFiles.value[fieldNumber - 1];

        containerFiles.value.splice(fieldNumber - 1, 2, fileToMoveUp, fileToMoveDown);
    }
}

function onRemoveFile(elem: FieldMovePayload) {
    const fieldNumber = getFieldNumberFromElement(elem);
    containerFiles.value.splice(fieldNumber, 1);
}

function fieldName(index: number) {
    return props.name + '[' + index + ']';
}

function addFile() {
    const fileField = {
        inFilelist: true,
        directory: props.directory,
        name: props.name,
        filelist: props.filelist,
        csrfToken: props.csrfToken,
        labels: props.labels,
        thumbnail: '',
        extensions: props.extensions,
        id: counter.value,
    };

    counter.value++;
    containerFiles.value.push(fileField);
}

defineExpose({
    counter,
    containerFiles,
    allowMore,
    isFirstInFilelist,
    isLastInFilelist,
    getFieldNumberFromElement,
    onMoveFileDown,
    onMoveFileUp,
    onRemoveFile,
    fieldName,
    addFile,
});
</script>
