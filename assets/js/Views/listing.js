import Vue from "vue";
/**
 * Editor Components
 */
import Row from "../Components/Listing/Row";
import Filter from "../Components/Listing/Filter";
/**
 * Register Components
 */
Vue.component("listing-row", Row);
Vue.component("listing-filter", Filter);

new Vue({ el: "#listing", name: "listing" });
