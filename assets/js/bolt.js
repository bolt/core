'use strict';

import Vue from 'vue'
import ElementUI from 'element-ui';
import 'element-ui/lib/theme-chalk/index.css';
import Hello from './Hello'

import '../css/bolt.css'

Vue.use(ElementUI);

// this loads jquery, but does *not* set a global $ or jQuery variable
// const $ = require('jquery');

console.log('joe');

new Vue({
    el: '#hello',
    template: '<Hello/>',
    components: { Hello }
  })

new Vue({
  el: '#app'
})