'use strict';

import Vue from 'vue'
import ElementUI from 'element-ui';
import 'element-ui/lib/theme-chalk/index.css';

// Bolt Components
import Hello from './Hello'
import Sidebar from './Sidebar'
import Topbar from './Topbar'
import DashboardNews from './DashboardNews'
import '../css/bolt.css'

Vue.use(ElementUI);
Vue.component('sidebar', Sidebar)
Vue.component('hello', Hello)
Vue.component('topbar', Topbar)
Vue.component('dashboardnews', DashboardNews)

// this loads jquery, but does *not* set a global $ or jQuery variable
// const $ = require('jquery');

new Vue({
  el: '#app'
});

// new Vue({
//   el: '#hello',
//   template: '<Hello/>',
//   components: { Hello }
// });

