import Vue from "vue";

//** Components */

import Slug from "../Components/Editor/Slug.vue";

Vue.component("editor-slug", Slug);

new Vue({ el: "#editcontent", name: "bolt-editor" });
