require('./bootstrap');

require('alpinejs');

import 'element-ui/lib/theme-chalk/index.css';

import VueAxios from 'vue-axios';
import axios from 'axios';
import Element from 'element-ui'

window.Vue = require('vue').default;

Vue.use(VueAxios, axios);
Vue.use(Element);

// Vue components
Vue.component('to-do', require('./components/ToDo.vue').default);

// Initialize Vue
const app = new Vue({
    el: '#app',
});
