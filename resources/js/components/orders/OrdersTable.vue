<template>
    <div class="container">
        <div class="card">
            <div class="card-header">
                <div class="row align-items-baseline">
                    <div class="col-md-6">Принятые заказы</div>
                    <label class="col-md-4 text-right" for="branch">Филиал</label>
                    <div class="col-md-2">
                        <select id="branch" class="form-control custom-select" v-model="selectedBranch">
                            <option v-for="branch in branches" :value="branch" :key="branch.id">{{branch.name}}</option>
                        </select>
                    </div>
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
            </b-table>

            <div class="card-footer">
                <pagination :data="pagination" @pagination-change-page="getOrders"></pagination>
            </div>
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
                required: true
            }
        },
        methods:{
            getOrders(page = 1){
                this.isBusy = true;
                let action = 'order/all';
                if(this.selectedBranch)
                    action = `/${this.selectedBranch.id}/orders`;
                axios.get(action)
                    .then(response=>{
                        this.pagination = response.data;
                        this.orders = response.data.data;
                        this.$nextTick(()=>{
                            this.isBusy = false;
                        })
                    });
            }
        },
        computed:{
            currentPage () {
                return  this.pagination.current_page;
            }
        },
        watch:{
            selectedBranch: function () {
                this.getOrders(this.currentPage);
            }
        },
        data() {
            return {
                selectedBranch: null,
                pagination:{},
                orders: null,
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
                    }
                }
            }
        },
        components:{
            'Pagination': require('laravel-vue-pagination')
        }
    }
</script>
