import Vue from "vue";
/**
 * Components
 */
import Text from "../Components/Editor/Text";
import Slug from "../Components/Editor/Slug";
import Textarea from "../Components/Editor/Textarea";
import Markdown from "../Components/Editor/Markdown";
import Html from "../Components/Editor/Html";
import DateTime from "../Components/Editor/DateTime";
import Select from "../Components/Editor/Select";
import Image from "../Components/Editor/Image";
/**
 * Register Components
 */
Vue.component("editor-text", Text);
Vue.component("editor-slug", Slug);
Vue.component("editor-textarea", Textarea);
Vue.component("editor-markdown", Markdown);
Vue.component("editor-html", Html);
Vue.component("editor-datetime", DateTime);
Vue.component("editor-select", Select);
Vue.component("editor-image", Image);

new Vue({ el: "#editcontent", name: "admin-editor" });
new Vue({ el: "#metadata", name: "admin-meta" });
