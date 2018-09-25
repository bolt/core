'use strict';

import Vue from 'vue';
import router from './router';
// import './registerServiceWorker'

// Bolt Components
import Hello from './Hello'
import Sidebar from './Sidebar'
import Topbar from './Topbar'
import DashboardNews from './DashboardNews'
import DashboardContentList from './DashboardContentList'
import App from './App'
import '../scss/bolt.scss'

// Vue.use(SuiVue);
Vue.component('sidebar', Sidebar)
Vue.component('hello', Hello)
Vue.component('topbar', Topbar)
Vue.component('dashboardnews', DashboardNews)
Vue.component('app', App)
Vue.component('dashboardcontentlist', DashboardContentList)

// This loads jquery, And sets a global $ and jQuery variable
const $ = require('jquery');
global.$ = global.jQuery = $;

new Vue({ el: 'header', router });
new Vue({ el: '#sidebar', router });
new Vue({ el: '#vuecontent', router });

new Vue({ el: 'dashboardnews' });

$(document).ready(function() {
    $('.ui.dropdown').dropdown();
});