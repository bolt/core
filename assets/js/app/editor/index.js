import Vue from 'vue';
/**
 * VueX Store
 */
import store from './store';
/**
 * Components
 */
import {
  Date,
  Embed,
  Html,
  Image,
  Markdown,
  Number,
  Select,
  Slug,
  Text,
  Textarea,
  Editor
} from './Components/Editor';

import { Language } from './Components/General';

// import Editor from 'vue-editor-js';

new Vue({
  store,
  el: '#editor',
  name: 'BoltEditor',
  components: {
    "editor-date": Date,
    "editor-embed": Embed,
    "editor-html": Html,
    "editor-image": Image,
    "editor-markdown": Markdown,
    "editor-number": Number,
    "editor-select": Select,
    "editor-slug": Slug,
    "editor-text": Text,
    "editor-textarea": Textarea,
    "general-language": Language,
    "editor": Editor
  }
});
