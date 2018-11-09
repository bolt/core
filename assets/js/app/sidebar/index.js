import Vue from "vue";
/**
 * VueX Store
 */
import store from './store'
/**
 * Components
 */
import Sidebar from "./Components/Sidebar.vue";

/**
 * Register Components
 */

new Vue({ 
  store,
  el: "#sidebar", 
  name: "bolt-sidebar",
  components: {
    "admin-sidebar": Sidebar
  }
});
