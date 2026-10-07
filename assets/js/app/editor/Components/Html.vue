<template>
    <div>
        <trumbowyg :id="id" v-model="val" :name="name" :config="config"></trumbowyg>
    </div>
</template>

<script>
import jQuery from 'jquery';
import trumbowyg from 'vue-trumbowyg';
import 'trumbowyg/dist/ui/trumbowyg.css';
import { strip } from '../../../filters/string';

// Trumbowyg's UI translations, one langs/<code>.min.js per language. Loading a file
// registers it on jQuery.trumbowyg.langs. English is built in.
const langFiles = require.context('trumbowyg/dist/langs', false, /\.min\.js$/);

// Trumbowyg names some languages differently from the ISO codes Bolt uses. Its `my`
// is Malay, while ISO `my` is Burmese, so that one must not match as-is.
const langAliases = new Map([
    ['be', 'by'],
    ['fil', 'ph'],
    ['ms', 'my'],
    ['my', null],
    ['nn', 'nb'],
    ['no', 'nb'],
    ['sr', 'rs'],
    ['sr_latn', 'rs_latin'],
    ['tl', 'ph'],
    ['uk', 'ua'],
    ['zh', 'zh_cn'],
    ['zh_hans', 'zh_cn'],
    ['zh_hant', 'zh_tw'],
    ['zh_hk', 'zh_tw'],
    ['zh_mo', 'zh_tw'],
]);

// Loads the Trumbowyg language matching a Bolt locale and returns its code, or 'en'
// when Trumbowyg has no translation for it. Tries the whole locale first, then drops
// one part at a time: `sr_Latn_RS` -> `sr_latn` -> `sr`, `de_AT` -> `de`.
function loadLang(locale) {
    const parts = locale.toLowerCase().replace(/-/g, '_').split('_');

    for (let length = parts.length; length > 0; length--) {
        const candidate = parts.slice(0, length).join('_');
        const lang = langAliases.has(candidate) ? langAliases.get(candidate) : candidate;
        const file = `./${lang}.min.js`;

        if (lang && langFiles.keys().includes(file)) {
            langFiles(file);

            // Arabic, Hebrew and Persian also set `_dir: 'rtl'`, which Trumbowyg applies
            // to the editable area. The text direction belongs to the content being
            // edited, not to the backend language, so only the labels are used.
            delete jQuery.trumbowyg.langs[lang]._dir;

            return lang;
        }
    }

    return 'en';
}

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
        locale: {
            type: String,
            default: 'en',
        },
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
    created() {
        // Must be set before Trumbowyg initialises, which happens when the child mounts.
        this.config.lang = loadLang(this.locale);
    },
    mounted() {
        this.val = strip(this.value);
    },
};
</script>
