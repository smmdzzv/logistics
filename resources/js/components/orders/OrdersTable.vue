<template>
    <div>
        <div class="alert alert-secondary">
            <div class="form-row align-items-baseline">
                <div class="form-group col-md">
                    <label for="clientCode">Код клиента</label>
                    <input id="clientCode" type="text" v-model.lazy="clientCode" class="form-control form-control-sm">
                </div>
                <div class="form-group col-md">
                    <label for="employeeCode">Код сотрудника</label>
                    <input id="employeeCode" type="text" v-model.lazy="employeeCode"
                           class="form-control form-control-sm">
                </div>
                <div class="form-group col-md">
                    <label for="minCubage">Мин кубатура</label>
                    <input id="minCubage" type="number" v-model.lazy="minCubage" class="form-control form-control-sm">
                </div>
                <div class="form-group col-md">
                    <label for="maxCubage">Макс кубатура</label>
                    <input id="maxCubage" type="number" v-model.lazy="maxCubage" class="form-control form-control-sm">
                </div>
                <div class="form-group col-md">
                    <label for="minWeight">Мин вес</label>
                    <input id="minWeight" type="number" v-model.lazy="minWeight" class="form-control form-control-sm">
                </div>
                <div class="form-group col-md">
                    <label for="maxWeight">Макс вес</label>
                    <input id="maxWeight" type="number" v-model.lazy="maxWeight" class="form-control form-control-sm">
                </div>
                <div class="form-group col-md">
                    <label for="minPrice">Мин цена</label>
                    <input id="minPrice" type="number" v-model.lazy="minPrice" class="form-control form-control-sm">
                </div>
                <div class="form-group col-md">
                    <label for="maxPrice">Макс цена</label>
                    <input id="maxPrice" type="number" v-model.lazy="maxPrice" class="form-control form-control-sm">
                </div>
                <div class="form-group col-md">
                    <label for="dateFrom">От</label>
                    <input id="dateFrom" type="date" v-model.lazy="dateFrom" class="form-control form-control-sm">
                </div>
                <div class="form-group col-md">
                    <label for="dateTo">До</label>
                    <input id="dateTo" type="date" v-model.lazy="dateTo" class="form-control form-control-sm">
                </div>
                <div class="form-group col-md">
                    <label for="status">Статус</label>
                    <select id="status" v-model.lazy="status" class="form-control form-control-sm">
                        <option :value="null">--Любые--</option>
                        <option value="accepted">Активный</option>
                        <option value="completed">Завершенный</option>
                    </select>
                </div>
            </div>
        </div>
        <table-card
            :customCells="customCells"
            :excelSheetName="selectedBranch ? selectedBranch.name : 'Все филиалы'"
            :fields="fields"
            :isBusy="isBusy"
            :items="orders"
            :setRowClass="setRowClass"
            striped
            class="shadow"
            excelFileName="Список заказов"
            primary-key="id"
            responsive>
            <template #header>
                <div class="row align-items-baseline">
                    <div class="col-6 col-md-4">Заказы</div>
                    <template v-if="branches">
                        <label class="col-6 col-md-4 text-right" for="branch">Филиалы</label>
                        <div class="col-md-4">
                            <select class="form-control custom-select" id="branch" v-model.lazy="selectedBranch">
                                <option :value="null">--Все филиалы--</option>
                                <option :key="branch.id" :value="branch" v-for="branch in branches">{{branch.name}}
                                </option>
                            </select>
                        </div>
                    </template>
                </div>
            </template>

            <template slot="details" slot-scope="{item}">
                <a :href="getDetailsUrl(item)" v-if="item.id !== 'dummyStatItem'"><img class="icon-btn-sm"
                                                                                       src="/svg/file.svg"></a>
            </template>

            <template slot="edit" slot-scope="{item}">
                <a v-if="item.status !== 'completed' && item.id !== 'dummyStatItem'" :href="getEditUrl(item)">
                    <img class="icon-btn-sm" src="/svg/edit.svg"></a>
            </template>

            <template #footer>
                <div class="card-footer">
                    <main-paginator :flowable="flowable" :onPageChange="getOrders"
                                    :pagination="pagination"></main-paginator>
                </div>
            </template>
        </table-card>
    </div>
</template>

<script>
    export default {
        name: "OrdersTable",
        mounted() {
            this.orders = this.providedOrders;

            if (this.loadItems)
                this.getOrders();
        },
        props: {
            branches: {
                type: Array,
                required: false,
            },
            url: {
                type: String,
                default: '/orders/all'
            },
            flowable: {
                type: Boolean,
                default: false
            },
            providedOrders: {
                type: Array
            },
            loadItems: {
                Type: Boolean,
                default: true
            }
        },
        methods: {
            prepareUrl() {
                let action = '/orders/filtered?';
                if (this.clientCode)
                    action += `client=${this.clientCode}&`;
                if (this.employeeCode)
                    action += `employee=${this.employeeCode}&`;
                if (this.minCubage)
                    action += `minCubage=${this.minCubage}&`;
                if (this.maxCubage)
                    action += `maxCubage=${this.maxCubage}&`;
                if (this.minWeight)
                    action += `minWeight=${this.minWeight}&`;
                if (this.maxWeight)
                    action += `maxWeight=${this.maxWeight}&`;
                if (this.minPrice)
                    action += `minPrice=${this.minPrice}&`;
                if (this.maxPrice)
                    action += `maxPrice=${this.maxPrice}&`;
                if (this.dateFrom)
                    action += `dateFrom=${this.dateFrom}&`;
                if (this.dateTo)
                    action += `dateTo=${this.dateTo}&`;
                if (this.selectedBranch)
                    action += `branch=${this.selectedBranch.id}&`;
                if(this.status)
                    action += `status=${this.status}&`;
                return action;
            },
            async getOrders(page = 1) {
                this.isBusy = true;
                // let action = this.action;
                // if (this.selectedBranch)
                //     action = `branch/${this.selectedBranch.id}/orders`;
                // action += '?paginate=7&page=' + page;
                let action = this.prepareUrl() + 'paginate=20&page=' + page;

                try {
                    const response = await axios.get(action);
                    this.pagination = response.data;
                    if (this.flowable)
                        response.data.data.forEach(item => {
                            this.orders.push(item);
                        });
                    else
                        this.orders = response.data.data;
                } catch (e) {

                }

                this.updateStat();

                this.$nextTick(() => {
                    this.isBusy = false;
                })
            },
            updateStat() {
                if (!this.orders)
                    return;

                let dummyStatItem = {
                    id: 'dummyStatItem',
                    owner: {code: 'Суммарные данные'},
                    totalPrice: 0,
                    totalWeight: 0,
                    totalDiscount: 0,
                    totalCubage: 0
                };

                for (let i = 0; i < this.orders.length; i++) {
                    dummyStatItem.totalPrice += this.orders[i].totalPrice;
                    dummyStatItem.totalWeight += this.orders[i].totalWeight;
                    dummyStatItem.totalDiscount += this.orders[i].totalDiscount;
                    dummyStatItem.totalCubage += this.orders[i].totalCubage;
                }

                this.orders.unshift(dummyStatItem)
            },
            getDetailsUrl(order) {
                return '/orders/' + order.id;
            },
            getEditUrl(order) {
                return '/orders/' + order.id + '/edit';
            },
            setRowClass(item, type) {
                if (item && item.id === 'dummyStatItem')
                    return 'table-success';
            }
        },
        computed: {
            currentPage() {
                return this.pagination.current_page;
            }
        },
        watch: {
            selectedBranch: function () {
                this.getOrders();
            },
            clientCode: function () {
                this.getOrders()
            },
            employeeCode: function () {
                this.getOrders()
            },
            minCubage: function () {
                this.getOrders()
            },
            maxCubage: function () {
                this.getOrders()
            },
            minWeight: function () {
                this.getOrders()
            },
            maxWeight: function () {
                this.getOrders()
            },
            minPrice: function () {
                this.getOrders()
            },
            maxPrice: function () {
                this.getOrders()
            },
            dateFrom: function () {
                this.getOrders()
            },
            dateTo: function () {
                this.getOrders()
            },
            status: function () {
                this.getOrders()
            }
        },
        data() {
            return {
                selectedBranch: null,
                pagination: {},
                orders: [],
                action: this.url,
                isBusy: false,
                customCells: ['details', 'edit'],
                clientCode: null,
                employeeCode: null,
                minCubage: null,
                maxCubage: null,
                minWeight: null,
                maxWeight: null,
                minPrice: null,
                maxPrice: null,
                dateFrom: null,
                dateTo: null,
                status:null,
                fields: {
                    'owner.code': {
                        label: 'Владелец',
                        sortable: true
                    },
                    totalWeight: {
                        label: 'Вес',
                        sortable: true
                    },
                    totalCubage: {
                        label: 'Кубатура',
                        sortable: true
                    },
                    totalDiscount: {
                        label: 'Скидка',
                        sortable: true
                    },
                    totalPrice: {
                        label: 'Цена',
                        sortable: true
                    },
                    'registeredBy.code': {
                        label: 'Принял',
                        sortable: true
                    },
                    created_at: {
                        label: 'Дата',
                        sortable: true
                    },
                    'details': {
                        label: '',
                    },
                    'edit': {
                        label: '',
                    }
                }
            }
        },
        components: {
            'MainPaginator': require('../common/MainPaginator.vue').default,
            'TableCard': require('../common/TableCard.vue').default
        }
    }
</script>
