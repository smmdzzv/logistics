<template>
    <table-card
        :customCells="customCells"
        :excelSheetName="selectedBranch ? selectedBranch.name : 'Все филиалы'"
        :fields="fields"
        :isBusy="isBusy"
        :items="orders"
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
                        <select class="form-control custom-select" id="branch" v-model="selectedBranch">
                            <option :value="null">--Все филиалы--</option>
                            <option :key="branch.id" :value="branch" v-for="branch in branches">{{branch.name}}</option>
                        </select>
                    </div>
                </template>
            </div>
        </template>

        <template slot="details" slot-scope="{item}">
            <a :href="getDetailsUrl(item)"><img class="icon-btn-sm" src="/svg/file.svg"></a>
        </template>

        <template slot="edit" slot-scope="{item}">
            <a v-if="item.status !== 'completed'" :href="getEditUrl(item)"><img class="icon-btn-sm" src="/svg/edit.svg"></a>
        </template>

        <template #footer>
            <div class="card-footer">
                <main-paginator :flowable="flowable" :onPageChange="getOrders"
                                :pagination="pagination"></main-paginator>
            </div>
        </template>
    </table-card>
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
            getOrders(page = 1) {
                this.isBusy = true;
                let action = this.action;
                if (this.selectedBranch)
                    action = `branch/${this.selectedBranch.id}/orders`;
                action += '?paginate=7&page=' + page;
                axios.get(action)
                    .then(response => {
                        this.pagination = response.data;
                        if (this.flowable)
                            response.data.data.forEach(item => {
                                this.orders.push(item);
                            });
                        else
                            this.orders = response.data.data;
                        this.$nextTick(() => {
                            this.isBusy = false;
                        })
                    });
            },
            getDetailsUrl(order) {
                return '/orders/' + order.id;
            },
            getEditUrl(order) {
                return '/orders/' + order.id + '/edit';
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
                fields: {
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
                    'registeredBy.name': {
                        label: 'Принял',
                        sortable: true
                    },
                    'owner.code': {
                        label: 'Владелец',
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
