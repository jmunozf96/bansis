/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */
require('./bootstrap');
require('jsrender');
require('bootstrap-select');
require('bootstrap-daterangepicker');
require('moment');
require('chart.js');
require('easy-autocomplete');
require('sweetalert2');
require('axios');
require('portal-vue');
require('bootstrap-vue');

window.Vue = require('vue');

// const files = require.context('./', true, /\.vue$/i);
// files.keys().map(key => Vue.component(key.split('/').pop().split('.')[0], files(key).default));


Vue.component('example-component', require('./components/ExampleComponent.vue').default);
Vue.component('empacadora-cajas', require('./components/empacadora/cajas.vue').default);
Vue.component('enfunde-egreso', require('./components/enfunde/enf_egreso.vue').default);
Vue.component('enfunde-registro', require('./components/enfunde/enf_enfunde.vue').default);
Vue.component('liquidacion-registro', require('./components/produccion/prd_liquidacion.vue').default);

/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */

const app = new Vue({
    el: '#app',
});
