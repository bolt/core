import Vue from "vue";
/**
 * Vue Core | Config
 */
import "./helpers/filters";
// import './registerServiceWorker'
/**
 * Bootstrap Javascript
 */
import "bootstrap";
/**
 * Styling
 */
import "../scss/bolt.scss";
/**
 * Set Axios
 */
import Axios from 'axios'
Vue.prototype.$axios = Axios;
/**
* Load jQuery
*/
import $ from 'jquery';
/**
 * Vue Components
 */
import "./Views/editor";
import "./Views/base";
