<template>
    <div>
        <textarea :id="name" ref="textarea" :name="name"></textarea>
    </div>
</template>

<script setup lang="ts">
import { ref, onMounted, onBeforeUnmount } from 'vue';
import EasyMDE from 'easymde';
import { strip } from '../../../filters/string';

const props = defineProps<{
    modelValue?: string;
    name?: string;
}>();

const emit = defineEmits<{
    (e: 'update:modelValue', value: string): void;
}>();

const textarea = ref<HTMLTextAreaElement | null>(null);
const easymde = ref<EasyMDE | null>(null);

defineExpose({ easymde });

onMounted(() => {
    easymde.value = new EasyMDE({
        element: textarea.value!,
        initialValue: strip(props.modelValue || ''),
        spellChecker: false,
        status: false,
        toolbar: [
            'bold',
            'italic',
            'heading',
            '|',
            'quote',
            'unordered-list',
            'ordered-list',
            '|',
            'link',
            'image',
            '|',
            'preview',
            'side-by-side',
            'fullscreen',
            '|',
            'guide',
        ],
        forceSync: true,
    });

    easymde.value.codemirror.on('change', () => {
        if (easymde.value) {
            const val = easymde.value.value();
            emit('update:modelValue', typeof val === 'string' ? val : '');
        }
    });
});

onBeforeUnmount(() => {
    if (easymde.value) {
        easymde.value.toTextArea();
        easymde.value = null;
    }
});
</script>

<style scoped>
@import '~easymde/dist/easymde.min.css';
</style>
