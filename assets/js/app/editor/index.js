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

const id = 'editor';
const editorSelector = '#' + id;
const editorPage = {
    data() {
        return {
            store,
            el: editorSelector,
            name: 'BoltEditor',
            mounted: function() {
                // Wait 2 seconds, so that Vue is initialised properly without triggering change events.
                setTimeout(function() {
                    UnsavedChangesWarning.warnFor(editorSelector + ' form');
                }, 2000);
            },
        }
    }
}

const app = createApp(editorPage);

if (document.getElementById(id)) {
    app.component('EditorCheckbox', Checkbox);
    app.component('EditorDate', Date);
    app.component('EditorEmbed', Embed);
    app.component('EditorEmail', Email);
    app.component('EditorPassword', Password);
    app.component('EditorHtml', Html); // Trumbowyg b0rk
    app.component('EditorImage', Image);
    app.component('EditorImageList', Imagelist); // b0rk
    app.component('EditorFile', File);
    app.component('EditorFileList', Filelist); // same b0rk as imagelist
    app.component('EditorMarkdown', Markdown); // Md itself works but the side by side seems to be broken
    app.component('EditorNumber', Number);
    app.component('EditorSelect', Select); // b0rk
    app.component('EditorSlug', Slug);
    app.component('EditorText', Text);
    app.component('EditorTextarea', Textarea);
    app.component('EditorCollection', Collection); 
    app.component('EditorLanguage', Language);
    app.component('ThemeSelect', ThemeSelect); 
    app.mount(editorSelector);
}
