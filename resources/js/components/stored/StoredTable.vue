<template>
    <div class="card">
        <slot name="header">
            <div class="card-header">
                <div class="row align-items-baseline">
                    <div class="col-md-6" v-if="branches">Товары на складе</div>
                    <div class="col-md-6" v-else>Товары на всех складах</div>
                    <template v-if="branches">
                        <label class="col-md-4 text-right" for="branch">Филиал</label>
                        <div class="col-md-2">
                            <select class="form-control custom-select" id="branch" v-model="selectedBranch">
                                <option :key="branch.id" :value="branch" v-for="branch in branches">{{branch.name}}
                                </option>
                            </select>
                        </div>
                    </template>
                </div>
            </div>
        </slot>

        <b-table :busy="isBusy"
                 :fields="fields"
                 :items="storedItems"
                 :selectable="selectable"
                 @row-clicked="itemSelected"
                 borderless
                 id="usersTable"
                 primary-key="id"
                 responsive
                 select-mode="single"
                 sticky-header="400px"
                 :tbody-tr-class="rowClass"
                 :striped="striped">
            <template v-slot:table-busy>
                <div class="text-center text-info my-2">
                    <b-spinner class="align-middle"></b-spinner>
                </div>
            </template>

            <template slot="selected" slot-scope="data">
                <span class="text-success" v-if="isSelected(data.item)">&check;</span>
                <span v-else></span>
            </template>
        </b-table>

        <div class="card-footer">
            <main-paginator :pagination="pagination" :onPageChange="getStoredItems" :flowable="flowable"></main-paginator>
        </div>
<!--        <template v-if="lastPage > 1">-->
<!--            <div class="card-footer text-center" v-if="flowablePagination && lastPage > currentPage">-->
<!--                <button @click="getStoredItems(currentPage+1)" class="btn btn-outline-primary align-middle">-->
<!--                    Загрузить еще-->
<!--                </button>-->
<!--            </div>-->
<!--            <div class="card-footer" v-if="!flowablePagination">-->
<!--                <pagination :data="pagination" @pagination-change-page="getStoredItems"/>-->
<!--            </div>-->
<!--        </template>-->
    </div>
</template>

<script>
    export default {
        name: "StoredTable",
        mounted() {
            if(this.items)
                this.storedItems = this.items;
            if(this.preselected)
                for(let pre of this.preselected){
                    this.selected.push(pre);
                }
            this.getStoredItems();
        },
        props: {
            branches: {
                type: Array,
                required: false,
                default: null
            },
            selectable: {
                type: Boolean,
                required: false,
                default: false
            },
            flowable: {
                type: Boolean,
                required: false,
                default: false
            },
            preselected: {
                type: Array,
                required: false,
                default: () => []
            },
            url: {
                type: String,
                required: false,
                default: '/stored/all'
            },
            items:{
                type: Array,
                required:false
            },
            striped:{
                type:Boolean,
                required:false,
                default:true
            }
        },
        methods: {
            getStoredItems(page = 1) {
                if(this.items)
                    return;
                this.isBusy = true;

                if (this.selectedBranch)
                    this.action = `/${this.selectedBranch.id}/stored`;
                this.action += '?paginate=7&page=' + page;
                axios.get(this.action)
                    .then(response => {
                        this.pagination = response.data;
                        if (this.flowable)
                            response.data.data.forEach(item => {
                                this.storedItems.push(item);
                            });
                        else
                            this.storedItems = response.data.data;
                        this.$nextTick(() => {
                            this.isBusy = false;
                        })
                    });
            },
            itemSelected(item) {
                if(!this.selectable)
                    return;
                if (this.isSelected(item)) {
                    this.selected = this.selected.filter(function (stored) {
                        return stored.id !== item.id
                    })
                } else {
                    this.selected.push(item)
                }

                return this.$emit('onItemsSelected', this.selected);
            },
            isSelected(item) {
                return this.selected.find(function (selected) {
                    return selected.id === item.id;
                });
            },
            rowClass(item, type) {
                if (!item) return;
                if (this.isSelected(item)) return 'table-success'
            }
        },
        computed: {
            currentPage() {
                return this.pagination.current_page;
            },
            lastPage() {
                return this.pagination.last_page
            }
        },
        watch: {
            selectedBranch: function () {
                this.selected.splice(0, this.selected.length);
                this.storedItems.splice(0, this.storedItems.length);
                this.$emit('onItemsSelected', this.selected);
                this.getStoredItems();
            }
        },
        data() {
            return {
                selectedBranch: null,
                selected: [],
                action: this.url,
                pagination: {
                    last_page: null,
                    current_page: null
                },
                storedItems: [],
                isBusy: false,
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
                    count: {
                        label: 'Кол-во',
                        sortable: true
                    },
                    'owner.name': {
                        label: 'Владелец',
                        sortable: true
                    },
                    'selected': {
                        label: ''
                    }
                }
            }
        },
        components:{
            'MainPaginator': require('../common/MainPaginator.vue').default
        }
    }
</script>
