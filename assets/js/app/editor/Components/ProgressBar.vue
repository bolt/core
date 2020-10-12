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

<script>
export default {
    name: 'ProgressBar',
    props: {
        max: {
            type: Number,
            default: 100,
        },
        value: {
            type: Number,
            default: 0,
        },
        height: {
            type: String,
            default: null,
        },
    },
    computed: {
        styleWidth() {
            return { width: 100 * (this.value / this.max) + '%' };
        },
        styleHeight() {
            return { height: this.height || null };
        },
    },
};
</script>
