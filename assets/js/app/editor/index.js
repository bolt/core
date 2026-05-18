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

Vue.component('EditorCheckbox', Checkbox);
Vue.component('EditorDate', Date);
Vue.component('EditorEmbed', Embed);
Vue.component('EditorEmail', Email);
Vue.component('EditorPassword', Password);
Vue.component('EditorHtml', Html);
Vue.component('EditorImage', Image);
Vue.component('EditorImagelist', Imagelist);
Vue.component('EditorFile', File);
Vue.component('EditorFilelist', Filelist);
Vue.component('EditorMarkdown', Markdown);
Vue.component('EditorNumber', Number);
Vue.component('EditorSelect', Select);
Vue.component('EditorSlug', Slug);
Vue.component('EditorText', Text);
Vue.component('EditorTextarea', Textarea);
Vue.component('EditorCollection', Collection);
Vue.component('EditorSet', Set);
Vue.component('GeneralLanguage', Language);
Vue.component('ThemeSelect', ThemeSelect);

const id = 'editor';
const editorSelector = '#' + id;

if (document.getElementById(id)) {
    new Vue({
        store,
        el: editorSelector,
        name: 'BoltEditor',
    });
}
