/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap');

let BootstrapVue = require('bootstrap-vue').default;
let Vuelidate = require('vuelidate').default;
let VueEasyPrint = require('vue-easy-print');
let VueExcelXlsx = require('vue-excel-xlsx').default;
let VueLuxon = require('vue-luxon');

window.Vue = require('vue');
window.Vue.use(BootstrapVue);
window.Vue.use(Vuelidate);
window.Vue.use(VueEasyPrint);
window.Vue.use(VueExcelXlsx);
window.Vue.use(VueLuxon,{
    serverZone: 'utc',
    serverFormat: 'sql',
    clientZone: 'Asia/Dushanbe',
    clientFormat: 'H:mm:ss dd-MM-yyyy',
    localeLang: null,
    localeFormat: {},
    diffForHumans: {},
    i18n: {}
});

/**
 * The following block of code may be used to automatically register your
 * Vue components. It will recursively scan this directory for the Vue
 * components and automatically register them with their "basename".
 *
 * Eg. ./components/SearchUserDropdown.vue -> <example-component></example-component>
 */

// const files = require.context('./', true, /\.vue$/i);
// files.keys().map(key => Vue.component(key.split('/').pop().split('.')[0], files(key).default));
// Vue.component('Profile', require('./components/users/Profile.vue').default);

Vue.component('UsersTable', require('./components/users/UsersTable.vue').default);
Vue.component('TrustedUserEditor', require('./components/users/trusted-users/TrustedUserEditor.vue').default);

Vue.component('StoredTable', require('./components/stored/StoredTable.vue').default);
Vue.component('SortableStoredTable', require('./components/stored/SortableStoredTable.vue').default);
Vue.component('StoredItemInfoTable', require('./components/stored-item-info/StoredItemInfoTable.vue').default);

Vue.component('OrdersTable', require('./components/orders/OrdersTable.vue').default);
Vue.component('OrderEditor', require('./components/orders/OrderEditor.vue').default);
Vue.component('OrderViewer', require('./components/orders/OrderViewer.vue').default);
Vue.component('OrderItemsListEditor', require('./components/orders/OrderItemsListEditor.vue').default);

Vue.component('TripsEditor', require('./components/trips/TripsEditor.vue').default);
Vue.component('TripItemsListEditor', require('./components/trips/TripItemsListEditor.vue').default);
Vue.component('TripItemsEditor', require('./components/trips/TripItemsEditor.vue').default);
Vue.component('LoadedItemsEditor', require('./components/trips/LoadedItemsEditor.vue').default);
Vue.component('UnloadedItemsEditor', require('./components/trips/UnloadedItemsEditor.vue').default);
Vue.component('TripExchangeItemsEditor', require('./components/trips/TripExchangeItemsEditor.vue').default);
Vue.component('TripsTable', require('./components/trips/TripsTable.vue').default);

Vue.component('CarsTable', require('./components/cars/CarsTable.vue').default);

Vue.component('TariffEditor', require('./components/tariffs/TariffEditor.vue').default);
Vue.component('TariffHistoriesViewer', require('./components/tariffs/TariffHistoriesViewer.vue').default);

Vue.component('IncomingPaymentEditor', require('./components/till/payments/incoming/IncomingPaymentEditor.vue').default);
Vue.component('OutgoingPaymentEditor', require('./components/till/payments/outgoing/OutgoingPaymentEditor.vue').default);
Vue.component('PaymentEditor', require('./components/till/payments/PaymentEditor.vue').default);

Vue.component('PaymentItemsTable', require('./components/till/expenditures/PaymentItemsTable.vue').default);

Vue.component('CurrenciesTable', require('./components/till/currencies/CurrenciesTable.vue').default);
Vue.component('PaymentsTable', require('./components/till/payments/PaymentsTable.vue').default);
Vue.component('PendingPaymentsTable', require('./components/till/payments/pending/PendingPaymentsTable.vue').default);

Vue.component('MoneyExchanger', require('./components/till/exchange/MoneyExchanger.vue').default);

Vue.component('ItemsTable', require('./components/items/ItemsTable.vue').default);

Vue.component('CustomsCodesTable', require('./components/customs/CustomsCodesTable.vue').default);

Vue.component('CreateLostItem', require('./components/lost-and-found/CreateLostItem.vue').default);

// Vue.component('DuobAccountsViewer', require('./components/accounts/branches/DuobAccountsViewer.vue').default);

Vue.component('MainPaginator', require('./components/common/MainPaginator.vue').default);

Vue.component('BranchViewer', require('./components/branches/BranchViewer.vue').default);
Vue.component('Barcode', require('@xkeshi/vue-barcode').default);
Vue.component('QrCode', require('@chenfengyuan/vue-qrcode').default);

/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */

// const app = new Vue({
//     el: '#app',
// });

window.app = new Vue({
    el: '#app',
    methods: {
        showErrorMsg(title, message) {
            this.$bvModal.msgBoxOk(message, {
                centered: true,
                okTitle: 'Закрыть',
                footerClass: 'border-0',
                title: title
            });
        }
    }
});
