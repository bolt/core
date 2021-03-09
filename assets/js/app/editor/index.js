import Vue from 'vue';
/**
 * VueX Store
 */
import store from './store';
/**
 * Components
 */
import Text from './Components/Text';
import Slug from './Components/Slug';
import Date from './Components/Date';
import Select from './Components/Select';
import Number from './Components/Number';
import Html from './Components/Html';
import Markdown from './Components/Markdown';
import Textarea from './Components/Textarea';
import Embed from './Components/Embed';
import Image from './Components/Image';
import Imagelist from './Components/Imagelist';
import Email from './Components/Email';
import Password from './Components/Password';
import ThemeSelect from './Components/ThemeSelect';
import Language from './Components/Language';
import File from './Components/File';
import Filelist from './Components/Filelist';
import Collection from './Components/Collection';
import Checkbox from './Components/Checkbox';
import UnsavedChangesWarning from './unsavedchanges';
import LivePreview from './Components/LivePreview';

Vue.component('editor-checkbox', Checkbox);
Vue.component('editor-date', Date);
Vue.component('editor-embed', Embed);
Vue.component('editor-email', Email);
Vue.component('editor-password', Password);
Vue.component('editor-html', Html);
Vue.component('editor-image', Image);
Vue.component('editor-imagelist', Imagelist);
Vue.component('editor-file', File);
Vue.component('editor-filelist', Filelist);
Vue.component('editor-markdown', Markdown);
Vue.component('editor-number', Number);
Vue.component('editor-select', Select);
Vue.component('editor-slug', Slug);
Vue.component('editor-text', Text);
Vue.component('editor-textarea', Textarea);
Vue.component('editor-collection', Collection);
Vue.component('editor-set', Set);
Vue.component('editor-live-preview', LivePreview);
Vue.component('general-language', Language);
Vue.component('theme-select', ThemeSelect);

const id = 'editor';
const editorSelector = '#' + id;

if (document.getElementById(id)) {
    new Vue({
        store,
        el: editorSelector,
        name: 'BoltEditor',
        mounted: function() {
            // Wait 2 seconds, so that Vue is initialised properly without triggering change events.
            setTimeout(function() {
                UnsavedChangesWarning.warnFor(editorSelector + ' form');
            }, 2000);
        },
    });
}
