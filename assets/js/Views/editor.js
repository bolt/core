import Vue from "vue";
/**
 * Editor Components
 */
import Slug from "../Components/Editor/Slug.vue";
import Textarea from "../Components/Editor/Textarea.vue";
import Markdown from "../Components/Editor/Markdown.vue";
import Html from "../Components/Editor/Html.vue";
import DateTime from "../Components/Editor/DateTime.vue";
import Select from "../Components/Editor/Select.vue";
import Image from "../Components/Editor/Image.vue";
/**
 * Register Components
 */
Vue.component("editor-slug", Slug);
Vue.component("editor-textarea", Textarea);
Vue.component("editor-markdown", Markdown);
Vue.component("editor-html", Html);
Vue.component("editor-datetime", DateTime);
Vue.component("editor-select", Select);
Vue.component("editor-image", Image);

new Vue({ el: "#editcontent", name: "admin-editor" });
new Vue({ el: "#metadata", name: "admin-meta" });
