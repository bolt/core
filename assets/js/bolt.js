import '@babel/polyfill';
import 'dropzone';
import 'bootstrap';
import Axios from 'axios';

Axios.defaults.headers.common = {
  accept: 'application/vnd.api+json',
};

import './jquery';
import './codemirror';
import './filters';
import './app';
import '../scss/bolt.scss';
