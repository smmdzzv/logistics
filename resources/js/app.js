/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap');
let BootstrapVue = require('bootstrap-vue').default;
let Vuelidate = require('vuelidate').default;

window.Vue = require('vue');
window.Vue.use(BootstrapVue);
window.Vue.use(Vuelidate);
/**
 * The following block of code may be used to automatically register your
 * Vue components. It will recursively scan this directory for the Vue
 * components and automatically register them with their "basename".
 *
 * Eg. ./components/SearchUserDropdown.vue -> <example-component></example-component>
 */

// const files = require.context('./', true, /\.vue$/i);
// files.keys().map(key => Vue.component(key.split('/').pop().split('.')[0], files(key).default));

Vue.component('OrderEditor', require('./components/OrderEditor.vue').default);
Vue.component('OrderViewer', require('./components/OrderViewer.vue').default);

Vue.component('TariffEditor', require('./components/tariffs/TariffEditor.vue').default);
Vue.component('TariffHistoriesViewer', require('./components/tariffs/TariffHistoriesViewer.vue').default);

Vue.component('Barcode', require('@xkeshi/vue-barcode').default);
Vue.component('QrCode', require('@chenfengyuan/vue-qrcode').default);
//Vue.component('search-user-dropdown', require('./components/SearchUserDropdown.vue').default);

/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */

const app = new Vue({
    el: '#app',
});
