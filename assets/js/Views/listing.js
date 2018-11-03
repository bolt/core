import Vue from "vue";
/**
 *   #rd Part Components
 */
import draggable from 'vuedraggable'
/**
 * Components
 */
import Filter from "../Components/Listing/Filter";
import Row from "../Components/Listing/Row";

/**
 * Register Components
 */
Vue.component("listing-filter", Filter);
Vue.component("listing-row", Row);
Vue.component("draggable", draggable);

new Vue({ el: "#listing", name: "listing" });
