import Vue from "vue";
/**
 * Components
 */
import Title from "./Components/Title";

/**
 * Register Components
 */

new Vue({ 
  el: "#title", 
  name: "bolt-title",
  components: {
    "bolt-title": Title
  }
});
