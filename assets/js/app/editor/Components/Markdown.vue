<template>
    <div>
        <markdown :id="name" v-model="val" :name="name" :configs="config" />
    </div>
</template>

<script>
import { formatStrip } from '../../../filters/string';

export default {
    name: 'EditorMarkdown',
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
        }
    },
    computed: {
        compiledMarkdown() {
            return marked(formatStrip(this.value));
        }
    },
    mounted() {
        this.val = formatStrip(this.value);
    },
};
</script>