import Vue from "vue";
/**
 * Components
 */
import Theme from "../Components/User/Theme";
import Password from "../Components/Password/Password";
import Email from "../Components/Email/Email";

/**
 * Register Components
 */
Vue.component("user-theme", Theme);
Vue.component("field-password", Password);
Vue.component("field-email", Email);

new Vue({ el: "#user", name: "user" });
new Vue({ el: "#user-password", name: "field-password" });
new Vue({ el: "#user-email", name: "field-password" });
