<template>
    <div>
        <textarea
            :id="id"
            v-model="val"
            class="form-control field--textarea"
            :name="name"
            :rows="rows"
            :required="required"
            :readonly="readonly"
            :data-errormessage="errormessage"
            :placeholder="placeholder"
            :style="{ height: styleHeight }"
            :maxlength="maxlength"
            :title="name"
            :configs="config"
        ></textarea>
    </div>
</template>

<script>
import { formatStrip } from '../../../filters/string';
import EditorTextarea from './Textarea';

export default {
    name: 'EditorMarkdown',
    components: {
        EditorTextarea,
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