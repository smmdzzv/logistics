<template>
        <div class="card">
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
            <b-table :busy="isBusy"
                     :fields="fields"
                     :items="storedItems"
                     :selectable="selectable"
                     select-mode="single"
                     @row-selected=""
                     @row-clicked="itemSelected"
                     borderless
                     id="usersTable"
                     primary-key="id"
                     responsive
                     striped>
                <template v-slot:table-busy>
                    <div class="text-center text-info my-2">
                        <b-spinner class="align-middle"></b-spinner>
                    </div>
                </template>

                <template slot="selected" slot-scope="data">
                    <span v-if="data.item.selected">&check;</span>
                    <span v-else></span>
                </template>
            </b-table>

            <template v-if="lastPage > 1">
                <div class="card-footer text-center" v-if="flowablePagination && lastPage > currentPage">
                    <button @click="getStoredItems(currentPage+1)" class="btn btn-outline-primary align-middle">
                        Загрузить еще
                    </button>
                </div>
                <div class="card-footer" v-if="!flowablePagination">
                    <pagination :data="pagination" @pagination-change-page="getStoredItems"/>
                </div>
            </template>
        </div>
</template>

<script>
    export default {
        name: "StoredTable",
        mounted() {
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
            flowablePagination: {
                type: Boolean,
                required: false,
                default: false
            }
        },
        methods: {
            getStoredItems(page = 1) {
                this.isBusy = true;
                let action = 'stored/all?page=' + page;
                if (this.selectedBranch)
                    action = `/${this.selectedBranch.id}/stored?page=${page}`;
                axios.get(action)
                    .then(response => {
                        this.pagination = response.data;
                        if (this.flowablePagination)
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
                if(item.selected) {
                     this.selected = this.selected.filter(function (stored) {
                         return stored.id !== item.id
                     })
                }
                else{
                    this.selected.push(item)
                }
                item.selected = !item.selected;
                return this.$emit('onItemsSelected', this.selected);
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
                this.getStoredItems(1);
            }
        },
        data() {
            return {
                selectedBranch: null,
                selected:[],
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
                        label:''
                    }
                }
            }
        },
        components: {
            'Pagination': require('laravel-vue-pagination')
        }
    }
</script>
