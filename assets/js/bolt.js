"use strict";
/**
 * ES7 Polyfil
 */
import "@babel/polyfill";
/**
 * Vue Core | Config
 */
import "./filters";
/**
 * Bootstrap Javascript
 */
import "bootstrap";
/**
 * Load Axios
 */
/**
 * Load jQuery
 */
import $ from "jquery";
/**
 * Vue Apps
 */
// import "./Views/editor";
import "./Views/user";

import "./app/notifications";
import "./app/toolbar";
import "./app/sidebar";
import "./app/listing";
import "./app/editor";
/**
 * Styling
 */
import "../scss/bolt.scss";

// import './registerServiceWorker'

window.$ = $;
window.jQuery = $;
