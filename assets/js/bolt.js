"use strict";

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
 * Vue Components
 */
import "./Views/editor";
import "./Views/base";

// This loads jquery, And sets a global $ and jQuery variable. We should keep it,
// for extensions / modules that require a global `$`.
const $ = require("jquery");
global.$ = global.jQuery = $;
