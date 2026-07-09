import { createApp } from 'vue';
import { createPinia } from 'pinia';
/**
 * Components
 */
import Text from './Components/Text.vue';
import Slug from './Components/Slug.vue';
import Date from './Components/Date.vue';
import Select from './Components/Select.vue';
import Number from './Components/Number.vue';
import Html from './Components/Html.vue';
import Markdown from './Components/Markdown.vue';
import Textarea from './Components/Textarea.vue';
import Embed from './Components/Embed.vue';
import Image from './Components/Image.vue';
import Imagelist from './Components/Imagelist.vue';
import Email from './Components/Email.vue';
import Password from './Components/Password.vue';
import ThemeSelect from './Components/ThemeSelect.vue';
import Language from './Components/Language.vue';
import File from './Components/File.vue';
import Filelist from './Components/Filelist.vue';
import Collection from './Components/Collection.vue';
import Checkbox from './Components/Checkbox.vue';

const app = createApp({
    name: 'BoltEditor',
});
app.config.compilerOptions.whitespace = 'preserve';

const pinia = createPinia();
app.use(pinia);

app.component('editor-checkbox', Checkbox);
app.component('editor-date', Date);
app.component('editor-embed', Embed);
app.component('editor-email', Email);
app.component('editor-password', Password);
app.component('editor-html', Html);
app.component('editor-image', Image);
app.component('editor-imagelist', Imagelist);
app.component('editor-file', File);
app.component('editor-filelist', Filelist);
app.component('editor-markdown', Markdown);
app.component('editor-number', Number);
app.component('editor-select', Select);
app.component('editor-slug', Slug);
app.component('editor-text', Text);
app.component('editor-textarea', Textarea);
app.component('editor-collection', Collection);
app.component('general-language', Language);
app.component('theme-select', ThemeSelect);

const id = 'editor';
const editorSelector = '#' + id;

if (document.getElementById(id)) {
    app.mount(editorSelector);
}
