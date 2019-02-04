import Vue from 'vue';
/**
 * VueX Store
 */
import store from './store';
/**
 * Components
 */
import Toolbar from './Components/Toolbar.vue';

/**
 * Register Components
 */

export default new Vue({
  store,
  el: '#toolbar',
  name: 'BoltToolbar',
  components: {
    'admin-toolbar': Toolbar,
  },
});
