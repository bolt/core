import Vue from "vue";
/**
 * Editor Components
 */
import Slug from "../Components/Editor/Slug.vue";
import TextArea from "../Components/Editor/Slug.vue";
import MarkDown from "../Components/Editor/Markdown.vue";
import Html from "../Components/Editor/Html.vue";
import DateTime from "../Components/Editor/DateTime.vue";
import Select from "../Components/Editor/Select.vue";
/**
 * Register Components
 */
Vue.component("editor-slug", Slug);
Vue.component("editor-textarea", TextArea);
Vue.component("editor-markdown", MarkDown);
Vue.component("editor-html", Html);
Vue.component("editor-datetime", DateTime);
Vue.component("editor-select", Select);

new Vue({ el: "#editcontent", name: "admin-editor" });
new Vue({ el: "#metadata", name: "admin-meta" });
