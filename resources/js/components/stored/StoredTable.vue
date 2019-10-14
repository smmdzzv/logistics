<template>
    <div class="card">
        <slot name="header">
            <div class="card-header">
                <div class="row align-items-baseline">
                    <div class="col-6 col-md-4">
                        <span v-if="branches">Товары на складе</span>
                        <span v-else>Товары на всех складах</span>
                    </div>
                    <template v-if="branches">
                        <label class="col-6 col-md-4 text-right" for="branch">Филиал</label>
                        <div class="col-md-4">
                            <select class="form-control custom-select" id="branch" v-model="selectedBranch">
                                <option :value="null">--Все склады--</option>
                                <option :key="branch.id" :value="branch" v-for="branch in branches">
                                    {{branch.name}}
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
                 :striped="striped"
                 :tbody-tr-class="rowClass"
                 @row-clicked="itemSelected"
                 borderless
                 id="usersTable"
                 primary-key="id"
                 responsive
                 select-mode="single"
                 sticky-header="400px">
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
            <main-paginator :flowable="flowable" :onPageChange="getStoredItems"
                            :pagination="pagination"></main-paginator>
        </div>
    </div>
</template>

<script>
    export default {
        name: "StoredTable",
        mounted() {
            if (this.items)
                this.setItems();
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
            loadData: {
                type: Boolean,
                required: false,
                default: true
            },
            selectedItems: {
                type: Array,
                required: false,
                default: () => []
            },
            url: {
                type: String,
                required: false,
                default: '/stored/all'
            },
            items: {
                type: Array,
                required: false
            },
            striped: {
                type: Boolean,
                required: false,
                default: true
            },
            prepareUrl: {
                type: Function,
                default: (page, vm) => {
                    let action = vm.url;
                    if (vm.selectedBranch)
                        action = `/${vm.selectedBranch.id}/stored`;
                    return action += '?paginate=7&page=' + page;
                }
            }
        },
        methods: {
            // Checks if item in all stored items array
            isInStoredItems(item) {
                return this.storedItems.find(function (stored) {
                    return stored.id === item.id;
                });
            },
            // Checks if item in provided list stored items,
            // which are items belonging to certain trip
            isInProvidedItems(item) {
                return this.items.find(function (stored) {
                    return stored.id === item.id;
                });
            },
            getStoredItems(page = 1) {
                if (!this.loadData)
                    return;

                this.isBusy = true;

                let action = this.prepareUrl(page, this);

                axios.get(action)
                    .then(response => {
                        this.pagination = response.data;
                        let items = response.data.data.filter(item => {
                            return !this.isInStoredItems(item)
                        });

                        if (this.flowable)
                            items.forEach(item => {
                                this.storedItems.push(item);
                            });
                        else {
                            this.storedItems = [...this.items, ...items];
                        }
                        this.$nextTick(() => {
                            this.isBusy = false;
                        })
                    });
            },
            itemSelected(item) {
                if (!this.selectable)
                    return;
                if (this.isSelected(item)) {
                    return this.$emit('onItemUnselected', item);
                } else {
                    return this.$emit('onItemSelected', item);
                }
            },
            isSelected(item) {
                return this.selectedItems.find(function (selected) {
                    return selected.id === item.id;
                });
            },
            rowClass(item, type) {
                if (!item) return;
                if (this.isSelected(item))
                    return 'table-success';
                else if (this.isInProvidedItems(item))
                    return 'table-danger';
            },
            setItems() {
                for (let item of this.items) {
                    this.storedItems.push(item);
                }

                if (this.selectedItems)
                    for (let item of this.selectedItems) {
                        if (!this.isInStoredItems(item))
                            this.storedItems.push(item);
                    }
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
                this.storedItems.splice(0, this.storedItems.length);
                this.setItems();
                this.getStoredItems();
            },
        },
        data() {
            return {
                selectedBranch: null,
                pagination: {
                    last_page: null,
                    current_page: null
                },
                storedItems: [],
                isBusy: false,
                fields: {
                    'info.item.name': {
                        label: 'Имя',
                        sortable: true
                    },
                    'info.width': {
                        label: 'Ширина',
                        sortable: true
                    },
                    'info.height': {
                        label: 'Высота',
                        sortable: true
                    },
                    'info.length': {
                        label: 'Длина',
                        sortable: true
                    },
                    'info.weight': {
                        label: 'Вес',
                        sortable: true
                    },
                    'info.owner.name': {
                        label: 'Владелец',
                        sortable: true
                    },
                    'selected': {
                        label: ''
                    }
                }
            }
        },
        components: {
            'MainPaginator': require('../common/MainPaginator.vue').default
        }
    }
</script>
