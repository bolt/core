<template>
    <div class="progress" :style="styleHeight">
        <div
            class="progress-bar"
            role="progressbar"
            aria-valuemin="0"
            :aria-valuenow="value"
            :aria-valuemax="max"
            :style="styleWidth"
        ></div>
    </div>
</template>

<script setup lang="ts">
import { computed } from 'vue';

const props = withDefaults(
    defineProps<{
        max?: number;
        value?: number;
        height?: string;
    }>(),
    {
        max: 100,
        value: 0,
        height: undefined,
    },
);

const styleWidth = computed(() => ({
    width: 100 * (props.value / props.max) + '%',
}));
const styleHeight = computed(() => ({ height: props.height || undefined }));
</script>
