import Vue from "vue";
/**
 * Components
 */
import Notification from "../Components/Base/Notification";
/**
 * Register Components
 */
Vue.component("bolt-notification", Notification);

new Vue({ el: "#notifications", name: "notifications" });