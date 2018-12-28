import Vue from "vue";
/**
 * VueX Store
 */
import store from './store'
/**
 * Components
 */
import {
  Text,
  Slug,
  Number,
  Date,
  Select,
  Html,
  Markdown,
  Textarea,
  Image
} from './Components/Editor';

import {
  Language
} from './Components/General';

new Vue({
  store,
  el: "#editor",
  name: "bolt-editor",
  components: {
    "general-language": Language,
    "editor-text": Text,
    "editor-slug": Slug,
    "editor-number": Number,
    "editor-date": Date,
    "editor-select": Select,
    "editor-html": Html,
    "editor-markdown": Markdown,
    "editor-textarea": Textarea,
    "editor-image": Image
  }
});
