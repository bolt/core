import Vue from "vue";
/**
 * VueX Store
 */
import store from './store'
/**
 * Components
 */
import Records from "./Components/Records";
import Filter from "./Components/Filter";
import Table from "./Components/Table";
import SelectBox from "./Components/SelectBox";

/**
 * Register Components
 */

new Vue({ 
  store,
  el: "#listing", 
  name: "bolt-listing",
  components: {
    "listing-records": Records,
    "listing-filter": Filter,
    "listing-table": Table,
    "listing-select-box": SelectBox
  }
});
