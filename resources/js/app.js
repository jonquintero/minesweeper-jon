require('./bootstrap');
window.Vue = require('vue');
window.EventBus = new Vue();
import VueRouter from 'vue-router';
import routes from './routes';
Vue.use(VueRouter);

import VueAxios from 'vue-axios';
import axios from 'axios';
import App from './App.vue';

Vue.use(VueAxios, axios);

const router = new VueRouter(routes);
let app = new Vue(Vue.util.extend({ router }, App)).$mount('#app');
