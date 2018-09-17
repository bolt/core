'use strict';

import Vue from 'vue'
import ElementUI from 'element-ui';
import router from './router';
import store from './store';
import 'element-ui/lib/theme-chalk/index.css';
import './registerServiceWorker'

// Bolt Components
import Hello from './Hello'
import Sidebar from './Sidebar'
import Topbar from './Topbar'
import DashboardNews from './DashboardNews'
import App from './App'
import '../css/bolt.css'

Vue.use(ElementUI);
Vue.component('sidebar', Sidebar)
Vue.component('hello', Hello)
Vue.component('topbar', Topbar)
Vue.component('dashboardnews', DashboardNews)
Vue.component('app', App)

// this loads jquery, but does *not* set a global $ or jQuery variable
// const $ = require('jquery');

new Vue({
  el: '#app',
  router,
  store,
});

// new Vue({
//   el: '#hello',
//   template: '<Hello/>',
//   components: { Hello }
// });

