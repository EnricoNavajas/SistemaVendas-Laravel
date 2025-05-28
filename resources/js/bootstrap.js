// resources/js/bootstrap.js

import _ from 'lodash'; 
window._ = _;

import axios from 'axios'; 
window.axios = axios;
window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';


import jQuery from 'jquery';
window.$ = window.jQuery = jQuery;

import * as bootstrap from 'bootstrap'; 
window.bootstrap = bootstrap;         