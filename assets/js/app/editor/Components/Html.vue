<template>
    <trumbowyg :id="id" v-model="val" :name="name" :config="config"></trumbowyg>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue';
import trumbowyg from 'vue-trumbowyg';
import 'trumbowyg/dist/ui/trumbowyg.css';
import { strip } from '../../../filters/string';

const props = defineProps<{
    value?: string;
    label?: string;
    name?: string;
    id?: string;
}>();

const val = ref('');
const config = {
    btns: [
        // ['undo', 'redo'],
        ['formatting'],
        ['strong', 'em', 'del'],
        ['link'],
        ['insertImage'],
        ['justifyLeft', 'justifyCenter', 'justifyRight'],
        ['unorderedList', 'orderedList'],
        ['horizontalRule'],
        ['removeformat'],
        ['fullscreen'],
        ['viewHTML'],
    ],
};

onMounted(() => {
    val.value = strip(props.value) ?? '';
});
</script>
