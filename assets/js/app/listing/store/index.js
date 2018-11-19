import Vue from 'vue';
import Vuex from 'vuex';
/**
 * modules
 */
import general from './modules/general/';
import listing from './modules/listing/';
import selecting from './modules/selecting/';

Vue.use(Vuex)

export default new Vuex.Store({
  modules: {
    general,
    listing,
    selecting
  },
});