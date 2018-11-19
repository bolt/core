import Vue from "vue";
/**
 * Components
 */
import Theme from "../Components/User/Theme";
import Password from "../Components/Password/Password";

/**
 * Register Components
 */
Vue.component("user-theme", Theme);
Vue.component("field-password", Password);

new Vue({ el: "#user", name: "user" });
new Vue({ el: "#field-password", name: "field-password" });
