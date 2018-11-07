"use strict";

/**
 * Vue Core | Config
 */
import Vue from "vue";
import "./helpers/filters";
// import './registerServiceWorker'

/**
 * Bootstrap Javascript
 */
import "bootstrap";

/**
 * Set Axios
 */
import Axios from 'axios'
Vue.prototype.$axios = Axios;

/**
* Load jQuery
*/
import $ from 'jquery';
window.$ = $;
window.jQuery = $;
/**
 * Helpers
 */
import './helpers/bootstrap';
/**
 * Vue Apps
 */
import "./Views/admin";
import "./Views/editor";
import "./Views/user";
import "./Views/notifications";


import "./app/listing";


/**
 * Styling
 */
import "../scss/bolt.scss";