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

            <template slot="groupedStoredItemsCount" slot-scope="{item}">
                <span>{{item.storedItems.length}}</span>
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
            this.setItems();
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
            providedItems: {
                type: Array
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
                customCells: ['selectedCount', 'groupedStoredItemsCount'],
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
                    'groupedStoredItemsStorage.name': {
                        label: 'Склад',
                        sortable: true
                    }
                }
            }
        },
        methods: {
            //Converts provided storedItem to StoredItemInfos
            convertStoredItemsToInfos(storedItems) {
                let groupedStoredItemsByInfo = storedItems.reduce((r, stored) => {
                    r[stored.info.id] = [...r[stored.info.id] || [], stored];
                    return r;
                }, {});

                let infos = Object.keys(groupedStoredItemsByInfo).map((key) => {
                    let storedItemInfo = Object.assign({}, groupedStoredItemsByInfo[key][0].info);
                    storedItemInfo.storedItems = groupedStoredItemsByInfo[key].map((stored) => {
                        let storedCopy = Object.assign({}, stored);
                        delete storedCopy.info;
                        return storedCopy;
                    });

                    if (storedItemInfo.storedItems)
                        storedItemInfo.selectedCount = storedItemInfo.storedItems.length;

                    return storedItemInfo;
                });

                return infos;
            },
            prepareStoredItemInfos(infos) {
                let storedItemInfos = [];
                let index = 0;

                infos.forEach(info => {
                    let groupedStoredItems = info.storedItems.reduce((r, stored) => {
                        r[stored.storageHistory.storage.id] = [...r[stored.storageHistory.storage.id] || [], stored];
                        return r;
                    }, {});

                    let infos = Object.keys(groupedStoredItems).map((key) => {
                        index++;

                        let storedItemInfo = Object.assign({}, info);
                        storedItemInfo.storedItems = groupedStoredItems[key];
                        storedItemInfo.primaryKey = storedItemInfo.id + storedItemInfo.storedItems[0].storageHistory.storage.id;
                        // storedItemInfo.groupedStoredItemsCount = storedItemInfo.storedItems.length;
                        storedItemInfo.groupedStoredItemsStorage = storedItemInfo.storedItems[0].storageHistory.storage;
                        storedItemInfo.selectedCount = !storedItemInfo.selectedCount ? 0 : storedItemInfo.selectedCount;
                        return storedItemInfo;
                    });

                    storedItemInfos = [...storedItemInfos, ...infos]
                });

                return storedItemInfos;
            },
            setItems() {
                if (this.providedItems) {
                    let storedItemInfos = this.convertStoredItemsToInfos(this.providedItems);
                    let storedItems = this.prepareStoredItemInfos(storedItemInfos);
                    for (let item of storedItems) {
                        this.items.push(item);
                    }
                }

                this.updateSelectedStoredItems();
            },
            getItems(page = 1) {
                this.isBusy = true;

                let action = this.prepareUrl(page, this);

                axios.get(action)
                    .then(response => {
                        this.pagination = response.data;
                        // let storedItemInfos = [];
                        // let index = 0;
                        // response.data.data.forEach(info => {
                        //     let groupedStoredItems = info.storedItems.reduce((r, stored) => {
                        //         r[stored.storageHistory.storage.branch.id] = [...r[stored.storageHistory.storage.branch.id] || [], stored];
                        //         return r;
                        //     }, {});
                        //
                        //     let infos = Object.keys(groupedStoredItems).map((key) => {
                        //         index++;
                        //
                        //         let storedItemInfo = Object.assign({}, info);
                        //         storedItemInfo.storedItems = groupedStoredItems[key];
                        //         storedItemInfo.primaryKey = storedItemInfo.id + storedItemInfo.storedItems[0].storageHistory.storage.branch.id;
                        //         storedItemInfo.groupedStoredItemsCount = storedItemInfo.storedItems.length;
                        //         storedItemInfo.groupedStoredItemsStorage = storedItemInfo.storedItems[0].storageHistory.storage.branch;
                        //         storedItemInfo.selectedCount = 0;
                        //         return storedItemInfo;
                        //     });
                        //
                        //     storedItemInfos = [...storedItemInfos, ...infos]
                        // });

                        let storedItemInfos = this.prepareStoredItemInfos(response.data.data);
                        let items = storedItemInfos.filter(item => {
                            let existingInfo = this.findInItems(item);
                            if (existingInfo) {
                                let newStoredItems = item.storedItems.filter((stored) => {
                                    return !existingInfo.storedItems.find((item) => {
                                        return item.id === stored.id;
                                    });
                                });

                                existingInfo.storedItems = [...existingInfo.storedItems, ...newStoredItems];
                            }

                            return !existingInfo;
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
            findInItems(item) {
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
                        } else break;
                    }
                });

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