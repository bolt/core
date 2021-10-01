<template>
    <div>
        <vue-simplemde :id="name" v-model="val" :name="name" :configs="config"></vue-simplemde>
    </div>
</template>

<script>
import VueSimplemde from 'vue-simplemde';
import { formatStrip } from '../../../filters/string';

export default {
    name: 'EditorMarkdown',
    components: {
        VueSimplemde,
    },
    props: {
        value: String,
        name: String,
    },
    data: () => {
        return {
            val: null,
            config: {
                spellChecker: false,
                status: false,
                toggleFullScreen: true,
            },
        };
    },
    mounted() {
        this.val = formatStrip(this.value);
    },
};
</script>

<style scoped>
@import '~simplemde/dist/simplemde.min.css';
</style>
