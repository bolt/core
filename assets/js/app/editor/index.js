import { createApp } from 'vue';
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

createApp().component('editor-checkbox', Checkbox);
createApp().component('editor-date', Date);
createApp().component('editor-embed', Embed);
createApp().component('editor-email', Email);
createApp().component('editor-password', Password);
createApp().component('editor-html', Html);
createApp().component('editor-image', Image);
createApp().component('editor-imagelist', Imagelist);
createApp().component('editor-file', File);
createApp().component('editor-filelist', Filelist);
createApp().component('editor-markdown', Markdown);
createApp().component('editor-number', Number);
createApp().component('editor-select', Select);
createApp().component('editor-slug', Slug);
createApp().component('editor-text', Text);
createApp().component('editor-textarea', Textarea);
createApp().component('editor-collection', Collection);
createApp().component('editor-set', Set);
createApp().component('general-language', Language);
createApp().component('theme-select', ThemeSelect);

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
