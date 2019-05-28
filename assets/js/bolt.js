import '@babel/polyfill';
import 'bootstrap';
import Axios from 'axios';

Axios.defaults.headers.common = {
  accept: 'application/vnd.api+json',
};

/**
 * Make jQuery available for modules/plugins that use it.
 */
import $ from 'jquery';
window.jQuery = $;
window.$ = $;

import './filters';
import './app';
import '../scss/bolt.scss';
