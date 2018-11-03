import Vue from "vue";
/**
 * Components
 */
import Theme from "../Components/User/Theme";


/**
 * Register Components
 */
Vue.component("user-theme", Theme);

new Vue({ el: "#user", name: "user" });
