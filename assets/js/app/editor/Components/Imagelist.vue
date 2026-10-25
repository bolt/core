<template>
    <div class="editor__imagelist">
        <div v-for="(child, activeIndex) in activeImageFields" :key="child.id" class="form-fieldsgroup">
            <editor-image
                :filename="child.filename"
                :thumbnail="child.thumbnail"
                :title="child.title"
                :alt="child.alt"
                :include-alt="true"
                :attributes-link="attributesLink"
                :media="child.media"
                :directory="directory"
                :filelist="filelist"
                :csrf-token="csrfToken"
                :labels="labelsValue"
                :in-imagelist="true"
                :name="fieldName(child.containerIndex)"
                :extensions="extensionsValue"
                :is-first-in-imagelist="isFirstInImagelist(activeIndex)"
                :is-last-in-imagelist="isLastInImagelist(activeIndex)"
                :readonly="readonly"
                :extra-fields="extraFieldsValue"
                :extra-data="child"
                @remove="onRemoveImage"
                @move-image-up="onMoveImageUp"
                @move-image-down="onMoveImageDown"
            ></editor-image>
        </div>
        <div v-if="activeImageFields.length === 0">
            <input :name="name" value="" type="hidden" />
        </div>

        <button class="btn btn-tertiary" type="button" :disabled="!allowMore" @click="addImage">
            <i class="fas fa-fw fa-plus"></i>
            {{ labelsValue.add_new_image }}
        </button>
    </div>
</template>

<script setup lang="ts">
import { ref, computed } from 'vue';
import EditorImage from './Image.vue';
import type { FieldMovePayload } from '../types';

type ExtraImageField = {
    label: string;
    placeholder?: string;
};

type ImageField = {
    id: number;
    containerIndex?: number;
    filename?: string;
    thumbnail?: string;
    title?: string;
    alt?: string;
    media?: string | number;
    hidden?: boolean;
    inImagelist?: boolean;
    directory?: string;
    name?: string;
    filelist?: string;
    csrfToken?: string;
    labels?: Record<string, string>;
    extensions?: string[];
    [key: string]: string | number | boolean | Record<string, string> | string[] | undefined;
};

const props = defineProps<{
    images?: Omit<ImageField, 'id'>[];
    directory?: string;
    name?: string;
    filelist?: string;
    csrfToken?: string;
    labels?: Record<string, string>;
    extensions?: string[];
    attributesLink?: string;
    limit?: number;
    readonly?: boolean;
    extraFields?: Record<string, ExtraImageField> | ExtraImageField[];
}>();

const counter = ref(0);
const containerImages = ref<ImageField[]>([]);
const labelsValue = computed(() => props.labels ?? {});
const extensionsValue = computed(() => props.extensions ?? []);
// `extraFields` comes from `field.definition.get('extra')` (imagelist.html.twig).
// A configured `extra` is always an associative map, so json_encode yields an
// object; when it's absent/empty, PHP serializes it as `[]` (an array). Treat any
// array as "no extra fields" — a populated indexed array is never a valid shape.
const extraFieldsValue = computed<Record<string, ExtraImageField>>(() =>
    Array.isArray(props.extraFields) ? {} : (props.extraFields ?? {}),
);

const initialImages = props.images || [];
initialImages.forEach((image, index) => {
    containerImages.value.push({ ...image, id: index });
    counter.value++;
});

const activeImageFields = computed(() =>
    containerImages.value
        .map((image, index) => ({ ...image, containerIndex: index }))
        .filter(image => image.hidden !== true),
);

const allowMore = computed(() => {
    if (props.readonly) {
        return false;
    }
    return activeImageFields.value.length < (props.limit ?? Infinity);
});

function isFirstInImagelist(index: number) {
    return index === 0;
}

function isLastInImagelist(index: number) {
    return index === activeImageFields.value.length - 1;
}

function getActiveImageFields() {
    return activeImageFields.value;
}

function getFieldNumberFromElement(elem: FieldMovePayload) {
    // get the last number because in collections, there are multiple.
    const matches = [...elem.fieldName.matchAll(/\d+/g)];
    const lastMatch = matches.splice(-1).pop();
    return lastMatch ? parseInt(lastMatch[0]) : 0;
}

function onMoveImageDown(elem: FieldMovePayload) {
    const fieldNumber = getFieldNumberFromElement(elem);
    const activeIndexes = getActiveContainerIndexes();
    const activePosition = activeIndexes.indexOf(fieldNumber);

    if (activePosition > -1 && activePosition < activeIndexes.length - 1) {
        const currentIndex = activeIndexes[activePosition];
        const nextIndex = activeIndexes[activePosition + 1];
        const imageToMoveDown = containerImages.value[currentIndex];
        const imageToMoveUp = containerImages.value[nextIndex];

        containerImages.value[currentIndex] = imageToMoveUp;
        containerImages.value[nextIndex] = imageToMoveDown;
    }
}

function onMoveImageUp(elem: FieldMovePayload) {
    const fieldNumber = getFieldNumberFromElement(elem);
    const activeIndexes = getActiveContainerIndexes();
    const activePosition = activeIndexes.indexOf(fieldNumber);

    if (activePosition > 0) {
        const currentIndex = activeIndexes[activePosition];
        const previousIndex = activeIndexes[activePosition - 1];
        const imageToMoveUp = containerImages.value[currentIndex];
        const imageToMoveDown = containerImages.value[previousIndex];

        containerImages.value[previousIndex] = imageToMoveUp;
        containerImages.value[currentIndex] = imageToMoveDown;
    }
}

function onRemoveImage(elem: FieldMovePayload) {
    const fieldNumber = getFieldNumberFromElement(elem);
    const updatedImage = containerImages.value[fieldNumber];
    updatedImage.hidden = true;
    containerImages.value[fieldNumber] = updatedImage;
}

function fieldName(index: number) {
    return props.name + '[' + index + ']';
}

function addImage() {
    const imageField = {
        inImagelist: true,
        directory: props.directory,
        name: props.name,
        filelist: props.filelist,
        csrfToken: props.csrfToken,
        labels: props.labels,
        thumbnail: '',
        extensions: props.extensions,
        id: counter.value,
        alt: '',
    };

    counter.value++;
    containerImages.value.push(imageField);
}

defineExpose({
    counter,
    containerImages,
    activeImageFields,
    allowMore,
    isFirstInImagelist,
    isLastInImagelist,
    getActiveImageFields,
    getFieldNumberFromElement,
    onMoveImageDown,
    onMoveImageUp,
    onRemoveImage,
    fieldName,
    addImage,
});

function getActiveContainerIndexes() {
    return containerImages.value
        .map((image, index) => (image.hidden === true ? null : index))
        .filter((index): index is number => index !== null);
}
</script>
