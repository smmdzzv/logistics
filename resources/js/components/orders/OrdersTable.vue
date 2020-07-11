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
            <div class="row">
                <button class="btn btn-primary mx-auto" @click="fetchFilteredData">Загрузить</button>
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

            <template v-slot:cell(totalWeight)="{item}">
                <span>{{item.totalWeight.toFixed(3)}}</span>
            </template>

            <template v-slot:cell(totalCubage)="{item}">
                <span>{{item.totalCubage.toFixed(3)}}</span>
            </template>

            <template v-slot:cell(weightPerCube)="{item}">
                <span>{{getWeightPerCube(item).toFixed(3)}}</span>
            </template>

            <template v-slot:cell(totalDiscount)="{item}">
                <span>{{item.totalDiscount.toFixed(2)}}</span>
            </template>

            <template v-slot:cell(totalPrice)="{item}">
                <span>{{item.totalPrice.toFixed(2)}}</span>
            </template>

            <template v-slot:cell(details)="{item}">
                <a :href="getDetailsUrl(item)"
                   v-if="item.id !== 'dummyStatItem' && item.id !== 'dummyStatItemPreviousData'">
                    <img class="icon-btn-sm" src="/svg/file.svg"></a>
            </template>

            <template v-slot:cell(edit)="{item}">
                <a v-if="item.status !== 'completed' && item.id !== 'dummyStatItem' && item.id !== 'dummyStatItemPreviousData'"
                   :href="getEditUrl(item)">
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
    import {hideBusySpinner, showBusySpinner} from '../../tools.js'

    let cancel;
    let CancelToken = axios.CancelToken;

    export default {
        name: "OrdersTable",
        mounted() {
            if (this.providedOrders)
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
                if (this.status)
                    action += `status=${this.status}&`;
                return action;
            },
            clearOrderStat() {
                this.orders = this.orders.filter((order) => order.id !== 'dummyStatItem');
            },
            clearOldStat() {
                this.orders = this.orders.filter((order) => order.id !== 'dummyStatItemPreviousData');
            },
            async getOrders(page = 1) {
                this.clearOrderStat();

                if ((cancel != undefined)) {
                    cancel();
                }

                // this.isBusy = true;
                showBusySpinner();
                let action = this.prepareUrl() + 'paginate=50&page=' + page;

                try {
                    const response = await axios.get(action,
                        {
                            cancelToken: new CancelToken(function executor(c) {
                                cancel = c;
                            })
                        });

                    this.pagination = response.data;
                    let orders = this.prepareOrders(response.data.data);
                    if (this.flowable)
                        orders.forEach(item => {
                            this.orders.push(item);
                        });
                    else
                        this.orders = orders;
                } catch (e) {

                }

                this.updateStat();

                this.$nextTick(() => {
                    // this.isBusy = false;
                    hideBusySpinner();
                })
            },
            prepareOrders(orders) {
                if (orders)
                    for (let i = 0; i < orders.length; i++) {
                        orders[i].placesCount = orders[i].storedItemInfos.reduce((sum, info) => sum + info.count, 0);
                    }

                return orders;
            },
            updateStat() {
                if (!this.orders)
                    return;

                let dummyStatItem = {
                    id: 'dummyStatItem',
                    owner: {code: 'Суммарные данные'},
                    placesCount: 0,
                    totalPrice: 0,
                    totalWeight: 0,
                    totalDiscount: 0,
                    totalCubage: 0
                };

                for (let i = 0; i < this.orders.length; i++) {
                    dummyStatItem.placesCount += this.orders[i].placesCount;
                    dummyStatItem.totalWeight += this.orders[i].totalWeight;
                    dummyStatItem.totalDiscount += this.orders[i].totalDiscount;
                    dummyStatItem.totalCubage += this.orders[i].totalCubage;
                    dummyStatItem.totalPrice += this.orders[i].totalPrice;
                }

                dummyStatItem.placesCount = Math.round(dummyStatItem.placesCount * 100) / 100;
                dummyStatItem.totalWeight = Math.round(dummyStatItem.totalWeight * 100) / 100;
                dummyStatItem.totalDiscount = Math.round(dummyStatItem.totalDiscount * 100) / 100;
                dummyStatItem.totalCubage = Math.round(dummyStatItem.totalCubage * 100) / 100;
                dummyStatItem.totalPrice = Math.round(dummyStatItem.totalPrice * 100) / 100;

                this.clearOrderStat();

                if (this.orders[0] && this.orders[0].id === 'dummyStatItemPreviousData') {
                    let previous = this.orders.shift();
                    this.orders.unshift(previous, dummyStatItem)
                } else
                    this.orders.unshift(dummyStatItem)
            },
            async getClientStat() {
                this.orders = this.orders.filter((order) => order.id !== 'dummyStatItemPreviousData');
                if (!this.clientCode || !this.dateFrom) {
                    return;
                }
                try {
                    let dateFrom = new Date().getFullYear() + '-01-01';
                    const response = await axios.get(
                        `/orders/${this.clientCode}/statistics?dateFrom=${dateFrom}&dateTo=${this.dateFrom}`
                    );
                    let dummyItem = response.data;
                    dummyItem.id = 'dummyStatItemPreviousData';
                    dummyItem.owner = {code: 'Данные до начала периода'};

                    this.clearOldStat();

                    this.orders.unshift(dummyItem);
                } catch (e) {
                }
            },
            getWeightPerCube(order) {
                return order.weightPerCube
                    = order.totalCubage ? Math.round(order.totalWeight / order.totalCubage * 100) / 100 : 0;
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
                if (item && item.id === 'dummyStatItemPreviousData')
                    return 'table-warning';
            },
            fetchFilteredData() {
                this.orders = [];
                this.getOrders();
                if (this.clientCode && this.dateFrom) {
                    this.getClientStat();
                }
            }
        },
        computed: {
            currentPage() {
                return this.pagination.current_page;
            }
        },
        data() {
            return {
                selectedBranch: null,
                pagination: {},
                orders: [],
                action: this.url,
                isBusy: false,
                customCells: ['weightPerCube', 'details', 'edit'],
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
                status: null,
                fields: [
                    {
                        key: 'owner.code',
                        label: 'Владелец',
                        sortable: true
                    },
                    {
                        key: 'placesCount',
                        label: 'Кол-во мест',
                        sortable: true
                    },
                    {
                        key: 'totalWeight',
                        label: 'Вес',
                        sortable: true
                    },
                    {
                        key: 'weightPerCube',
                        label: 'Кг в 1 кубе',
                        sortable: true
                    },
                    {
                        key: 'totalCubage',
                        label: 'Кубатура',
                        sortable: true
                    },
                    {
                        key: 'totalDiscount',
                        label: 'Скидка',
                        sortable: true
                    },
                    {
                        key: 'totalPrice',
                        label: 'Цена',
                        sortable: true
                    },
                    {
                        key: 'creator.code',
                        label: 'Принял',
                        sortable: true
                    },
                    {
                        key: 'created_at',
                        label: 'Дата',
                        sortable: true
                    },
                    {
                        key: 'details',
                        label: '',
                    },
                    {
                        key: 'edit',
                        label: '',
                    }
                ]
            }
        },
        components: {
            'MainPaginator': require('../common/MainPaginator.vue').default,
            'TableCard': require('../common/TableCard.vue').default
        }
    }
</script>
