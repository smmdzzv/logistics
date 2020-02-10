<template>
    <div>
        <table-card
                :borderless="borderless"
                :fields="fields"
                :fixed="fixed"
                :hover="hover"
                :items="items"
                :responsive="responsive"
                :select-mode="selectMode"
                :selectable="selectable"
                :sticky-header="tableHeight"
                :striped="striped"
                :tableBusy="false"
                :customCells="customCells"
                excelFileName="Список доступных товаров"
                excelSheetName="Лист 1"
                :primaryKey="'primaryKey'"
                responsive>
            <template slot="selectedCount" slot-scope="{item}">
                <input class="form-control" type="text" maxlength="3" v-model="item.selectedCount"
                       @change="updateSelectedStoredItems">
            </template>
        </table-card>

        <div class="card-footer">
            <main-paginator :flowable="flowable"
                            :onPageChange="getItems"
                            :pagination="pagination"/>
        </div>
    </div>
</template>

<script>
    import ExcelDataPreparatory from '../common/ExcelDataPreparatory.vue'
    import TableCardProps from '../common/TableCardProps.vue'

    export default {
        name: "StoredItemInfoTable",
        mixins: [ExcelDataPreparatory, TableCardProps],
        mounted() {
            this.getItems();
        },
        props: {
            branches: {
                type: Array
            },
            url: {
                type: String,
                default: 'stored-item-info/filtered'
            },
            prepareUrl: {
                type: Function,
                default: (page, vm) => {
                    let action = vm.url;
                    if (vm.selectedBranch)
                        action = `/${vm.selectedBranch.id}/stored`;
                    return action += '?paginate=40&page=' + page;
                }
            },
            flowable: false
        },
        data() {
            return {
                selectedBranch: null,
                pagination: {
                    last_page: null,
                    current_page: null
                },
                isBusy: false,
                items: [],
                customCells: ['selectedCount'],
                fields: {
                    'owner.code': {
                        label: 'Владелец',
                        sortable: true
                    },
                    'item.name': {
                        label: 'Наименование',
                        sortable: true
                    },
                    'width': {
                        label: 'Ширина',
                        sortable: true
                    },
                    'height': {
                        label: 'Высота',
                        sortable: true
                    },
                    'length': {
                        label: 'Длина',
                        sortable: true
                    },
                    'weight': {
                        label: 'Вес',
                        sortable: true
                    },
                    'groupedStoredItemsCount': {
                        label: 'Остаток',
                        sortable: true
                    },
                    'selectedCount': {
                        label: 'Кол-во мест'
                    },
                    'groupedStoredItemsBranch.name': {
                        label: 'Склад',
                        sortable: true
                    }
                }
            }
        },
        methods: {
            getItems(page = 1) {
                this.isBusy = true;

                let action = this.prepareUrl(page, this);

                axios.get(action)
                    .then(response => {
                        this.pagination = response.data;
                        let storedItemInfos = [];
                        let index = 0;
                        response.data.data.forEach(info => {
                            let groupedStoredItems = info.storedItems.reduce((r, stored) => {
                                r[stored.storageHistory.storage.branch.id] = [...r[stored.storageHistory.storage.branch.id] || [], stored];
                                return r;
                            }, {});

                            let infos = Object.keys(groupedStoredItems).map((key) => {
                                index++;

                                let storedItemInfo = Object.assign({}, info);
                                storedItemInfo.storedItems = groupedStoredItems[key];
                                storedItemInfo.primaryKey = storedItemInfo.id + storedItemInfo.storedItems[0].storageHistory.storage.branch.id;
                                storedItemInfo.groupedStoredItemsCount = storedItemInfo.storedItems.length;
                                storedItemInfo.groupedStoredItemsBranch = storedItemInfo.storedItems[0].storageHistory.storage.branch;
                                storedItemInfo.selectedCount = 0;
                                return storedItemInfo;
                            });

                            storedItemInfos = [...storedItemInfos, ...infos]
                            // groupedStoredItems.forEach(grouped => {
                            //     storedItemInfo = Object.assign({}, info);
                            //     storedItemInfo.storedItems = grouped;
                            //     storedItemInfos.push(storedItemInfo);
                            // });
                        });


                        // response.data.data.forEach(info => {
                        //     info.storedItems.forEach(stored => {
                        //         console.log(stored.storageHistory.storage.branch.id)
                        //     })
                        // });

                        let items = storedItemInfos.filter(item => {
                            return !this.isInItems(item)
                        });

                        if (this.flowable)
                            items.forEach(item => {
                                this.items.push(item);
                            });
                        else {
                            this.items = [...this.items, ...items];
                        }
                        this.$nextTick(() => {
                            this.isBusy = false;
                        })
                    });
            },
            // rowClass(item, type) {
            //     if (!this.highlightRows || !item) return;
            //     if (this.isSelected(item))
            //         return 'table-success';
            //     else if (this.isInProvidedItems(item))
            //         return 'table-danger';
            // },
            // Checks if item in all items array
            isInItems(item) {
                return this.items.find(function (stored) {
                    return stored.primaryKey === item.primaryKey;
                });
            },
            updateSelectedStoredItems() {
                let selectedItems = [];
                this.items.forEach((storedItemInfo) => {
                    let count = 0;

                    for (let i = 0; i < storedItemInfo.storedItems.length; i++) {
                        //Adapter -> to match grouped table view selection with existing TripItemsEditor;
                        let storedItemInfoCopy = Object.assign({}, storedItemInfo);
                        delete storedItemInfoCopy.storedItems;

                        if (storedItemInfo.selectedCount > count) {
                            let storedItem = Object.assign({}, storedItemInfo.storedItems[i]);
                            storedItem.info = storedItemInfoCopy;
                            selectedItems.push(storedItem);
                            count++;
                        }
                        else break;
                    }
                });
                console.log(selectedItems);
                this.$emit('onItemsSelected', selectedItems);
            }
        },
        components: {
            'TableCard': require('../common/TableCard.vue').default
        }
    }
</script>

<style scoped>

</style>