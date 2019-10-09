<template>
        <div class="card shadow">
            <div class="card-header">
                <div class="row align-items-baseline">
                    <div class="col-6 col-md-4">Заказы</div>
                    <template  v-if="branches" >
                        <label class="col-6 col-md-4 text-right" for="branch">Филиалы</label>
                        <div class="col-md-4">
                            <select id="branch" class="form-control custom-select" v-model="selectedBranch">
                                <option v-for="branch in branches" :value="branch" :key="branch.id">{{branch.name}}</option>
                            </select>
                        </div>
                    </template>
                </div>
            </div>
            <b-table :fields="fields"
                     id="usersTable"
                     :items="orders"
                     :busy="isBusy"
                     striped
                     borderless
                     primary-key="id"
                     responsive>
                <template v-slot:table-busy>
                    <div class="text-center text-info my-2">
                        <b-spinner class="align-middle"></b-spinner>
                    </div>
                </template>

                <template slot="details" slot-scope="{item}">
                    <a class="btn btn-outline-primary" :href="getDetailsUrl(item)">Детали</a>
                </template>
            </b-table>
            <div class="card-footer">
<!--                <pagination :data="pagination" @pagination-change-page="getOrders"></pagination>-->
                <main-paginator :pagination="pagination" :onPageChange="getOrders" :flowable="flowable"></main-paginator>
            </div>
        </div>
</template>

<script>
    export default {
        name: "OrdersTable",
        mounted(){
            this.getOrders();
        },
        props:{
            branches:{
                type:Array,
                required: false,
            },
            url:{
                type:String,
                default:'/orders/all'
            },
            flowable:{
                type:Boolean,
                default:false
            }
        },
        methods:{
            getOrders(page = 1){
                this.isBusy = true;
                if(this.selectedBranch)
                    this.action = `branch/${this.selectedBranch.id}/orders`;
                this.action += '?paginate=7&page=' + page;
                axios.get(this.action)
                    .then(response=>{
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
            getDetailsUrl(order){
                return '/orders/' + order.id;
            }
        },
        computed:{
            currentPage () {
                return  this.pagination.current_page;
            }
        },
        watch:{
            selectedBranch: function () {
                this.getOrders();
            }
        },
        data() {
            return {
                selectedBranch: null,
                pagination:{},
                orders: [],
                action: this.url,
                isBusy:false,
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
                    'registered_by.name':{
                        label:'Принял',
                        sortable: true
                    },
                    'owner.name':{
                        label:'Владелец',
                        sortable: true
                    },
                    created_at:{
                        label:'Дата',
                        sortable: true
                    },
                    'details':{
                        label:'',
                    }
                }
            }
        },
        components:{
            'MainPaginator': require('../common/MainPaginator.vue').default
        }
    }
</script>
