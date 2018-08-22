'use strict';

import Vue from 'vue'
import ElementUI from 'element-ui';
import 'element-ui/lib/theme-chalk/index.css';

// Bolt Components
import Hello from './Hello'
import Sidebar from './Sidebar'
import Topbar from './Topbar'
import '../css/bolt.css'

Vue.use(ElementUI);

// this loads jquery, but does *not* set a global $ or jQuery variable
// const $ = require('jquery');

new Vue({
  el: '#app'
});

new Vue({
  el: '#hello',
  template: '<Hello/>',
  components: { Hello }
});

new Vue({
  el: 'sidebar',
  template: '<Sidebar/>',
  components: { Sidebar }
});

new Vue({
  el: 'topbar',
  template: '<Topbar/>',
  components: { Topbar }
});
