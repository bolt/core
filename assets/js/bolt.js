import '@babel/polyfill';
import Axios from 'axios';

Axios.defaults.headers.common = {
  accept: 'application/vnd.api+json',
};

import './filters';
import './app';
import '../scss/bolt.scss';
