import Vue from "vue";
/**
 * Components
 */
import Theme from "../Components/User/Theme";
import Password from "../Components/Password/Password";
import Email from "../Components/Email/Email";
import Text from "../app/editor/Components/Editor/Text/Text";

/**
 * Register Components
 */
Vue.component("user-theme", Theme);
Vue.component("field-password", Password);
Vue.component("field-email", Email);
Vue.component("editor-text", Text);

new Vue({
    el: "#login-form",
    name: "bolt-login",
    components: {
        "editor-text": Text,
        "field-password": Password,
    }
})

