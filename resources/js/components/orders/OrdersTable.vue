<template>
        <div class="card">
            <div class="card-header">
                <div class="row align-items-baseline">
                    <div class="col-md-4">Заказы</div>
                    <template  v-if="branches" >
                        <label class="col-md-4 text-right" for="branch">Филиал</label>
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
            </b-table>
            <div class="card-footer">
                <pagination :data="pagination" @pagination-change-page="getOrders"></pagination>
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
            action:{
                type:String,
                required: false
            }
        },
        methods:{
            getOrders(page = 1){
                this.isBusy = true;
                if(this.selectedBranch)
                    this.action = `branch/${this.selectedBranch.id}/orders`;
                axios.get(this.action)
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
                orders: [],
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
