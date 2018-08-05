'use strict';

import Vue from 'vue'
import Hello from './Hello'
// import Buefy from 'buefy'
// import 'buefy/lib/buefy.css'

import '../css/bolt.css'

// Vue.use(Buefy);

// this loads jquery, but does *not* set a global $ or jQuery variable
// const $ = require('jquery');

console.log('joe');

new Vue({
    el: '#hello',
    template: '<Hello/>',
    components: { Hello }
  })
