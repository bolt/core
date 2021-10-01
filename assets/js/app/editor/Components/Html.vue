<template>
    <div>
        <trumbowyg :id="id" v-model="val" :name="name" :config="config"></trumbowyg>
    </div>
</template>

<script>
import trumbowyg from 'vue-trumbowyg';
import { formatStrip } from '../../../filters/string'
import 'trumbowyg/dist/ui/trumbowyg.css';

export default {
    name: 'EditorHtml',
    components: {
        trumbowyg,
    },
    props: {
        value: String,
        label: String,
        name: String,
        id: String,
    },
    data: () => {
        return {
            val: null,
            config: {
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
            },
        };
    },
    mounted() {
        this.val = formatStrip(this.value);
    },
};
</script>
