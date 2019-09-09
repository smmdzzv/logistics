<template>
    <div class="container">
        <div class="card">
            <div class="card-header">
                <div class="row align-items-baseline">
                    <div class="col-md-9"> Принятые товары</div>
                    <label class="col-md-1 text-right" for="branch">Филиал</label>
                    <div class="col-md-2">
                        <select id="branch" class="form-control custom-select" v-model="selectedBranch">
                            <option v-for="branch in branches" :value="branch" :key="branch.id">{{branch.name}}</option>
                        </select>
                    </div>
                </div>
            </div>
                <b-table :fields="fields"
                         id="usersTable"
                         :items="storedItems"
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
                <pagination :data="pagination" @pagination-change-page="getStoredItems"></pagination>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        name: "StoredTable",
        mounted(){
            this.getStoredItems();
        },
        props:{
            branches:{
                type:Array,
                required: true
            }
        },
        methods:{
            getStoredItems(page = 1){
                this.isBusy = true;
                let action = 'stored/all';
                if(this.selectedBranch)
                    action = `/${this.selectedBranch.id}/stored`;
                axios.get(action)
                    .then(response=>{
                        this.pagination = response.data;
                        this.storedItems = response.data.data;
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
                this.getStoredItems(this.currentPage);
            }
        },
        data() {
            return {
                selectedBranch: null,
                pagination:{},
                storedItems: null,
                isBusy:false,
                fields: {
                    'item.name': {
                        label: 'Имя',
                        sortable: true
                    },
                    width: {
                        label: 'Ширина',
                        sortable: true
                    },
                    height: {
                        label: 'Высота',
                        sortable: true
                    },
                    length: {
                        label: 'Длина',
                        sortable: true
                    },
                    count:{
                        label:'Кол-во',
                        sortable: true
                    },
                    'owner.name':{
                        label:'Владелец',
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
