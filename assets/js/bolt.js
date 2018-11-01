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
 * Loop Static Assets
 */
const imagesCtx = require.context('../static/', true, /\.(png|jpg|jpeg|gif|ico|svg|webp)$/);
imagesCtx.keys().forEach(imagesCtx);

/**
* Load jQuery
*/
import $ from 'jquery';
window.$ = $;
window.jQuery = $;

/**
 * Vue Components
 */
import "./Views/admin";
import "./Views/listing";
import "./Views/editor";

/**
 * Styling
 */
import "../scss/bolt.scss";
