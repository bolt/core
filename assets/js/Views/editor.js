import Vue from "vue";

//** Components */

import Slug from "../Components/Editor/Slug.vue";
import TextArea from "../Components/Editor/Slug.vue";
import Markdown from "../Components/Editor/Markdown.vue";
import Html from "../Components/Editor/Html.vue";

Vue.component("editor-slug", Slug);
Vue.component("editor-textarea", TextArea);
Vue.component("editor-markdown", Markdown);
Vue.component("editor-html", Html);

new Vue({ el: "#editcontent", name: "bolt-editor" });
