import Vue from 'vue';
import store from './store';
import Sidebar from './Components/Sidebar.vue';

new Vue({
  store,
  el: '#sidebar',
  name: 'BoltSidebar',
  components: {
    'admin-sidebar': Sidebar,
  },
});
