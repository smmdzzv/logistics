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
            :striped="striped"
            :tableBusy="false"
            :setRowClass="setRowClass"
            excelFileName="Список товаров"
            :excelSheetName="excelSheetName"
            :primaryKey="'primaryKey'"
            responsive>
            <template slot="header">
                <div class="row">
                    <span class="ml-2">
                        Список товаров
                    </span>

                    <template v-if="branches">
                        <div class="ml-auto">
                            <select class="form-control custom-select" id="branch" v-model="selectedBranch">
                                <option :value="null">--Все склады--</option>
                                <option :key="branch.id" :value="branch" v-for="branch in branches">
                                    {{ branch.name }}
                                </option>
                            </select>
                        </div>
                    </template>
                </div>
            </template>

            <template v-slot:cell(weight)="{item}">
                <span v-if="item.type !== 'dummy'">{{ item.weight.toFixed(3) }}</span>
            </template>

            <template v-slot:cell(width)="{item}">
                <span v-if="item.type !== 'dummy'">{{ item.width.toFixed(3) }}</span>
            </template>

            <template v-slot:cell(height)="{item}">
                <span v-if="item.type !== 'dummy'">{{ item.height.toFixed(3) }}</span>
            </template>

            <template v-slot:cell(length)="{item}">
                <span v-if="item.type !== 'dummy'">{{ item.length.toFixed(3) }}</span>
            </template>

            <template v-slot:cell(cubage)="{item}">
                <span v-if="item.type !== 'dummy'">{{ getCubage(item).toFixed(3) }}</span>
            </template>

            <template v-slot:cell(totalCubage)="{item}">
                <span>{{ getTotalCubage(item).toFixed(3) }}</span>
            </template>

            <template v-slot:cell(totalWeight)="{item}">
                <span>{{ getTotalWeight(item).toFixed(3) }}</span>
            </template>

            <template v-slot:cell(weightPerCube)="{item}">
                <span>{{ getWeightPerCube(item).toFixed(3) }}</span>
            </template>

            <template v-slot:cell(selectedCount)="{item}">
                <input class="form-control" type="text" maxlength="3" style="width:6em" v-model="item.selectedCount"
                       @change="updateSelectedStoredItems">
            </template>

            <template v-slot:cell(groupedStoredItemsCount)="{item}">
                <span>{{ getItemsLength(item) }}</span>
            </template>

            <template v-slot:cell(totalPrice)="{item}">
                <span>{{ getTotalPrice(item).toFixed(2) }}</span>
            </template>

            <template v-slot:cell(edit)="row">
                <div class="d-flex" v-if="row.item.type !== 'dummy'">
                    <button class="btn" @click="row.toggleDetails">
                        <b-icon-list></b-icon-list>
                    </button>
                    <!--                    <a class="btn" :id="row.item.id" href="#" @click.prevent="onLostItemSelected(row.item)">-->
                    <!--                        <img class="icon-btn-sm" src="/svg/lost.svg" alt="">-->
                    <!--                    </a>-->
                    <b-tooltip :target="row.item.id" triggers="hover">
                        Отметить товар утерянным
                    </b-tooltip>
                </div>
            </template>

            <template v-slot:row-details="{item}">
                <stored-item-rows :stored-items="item.storedItems"></stored-item-rows>
            </template>
        </table-card>

        <div class="card-footer" v-if="pagination && pagination.last_page > 1">
            <main-paginator :flowable="flowable"
                            :onPageChange="getItems"
                            :pagination="pagination"/>
        </div>
    </div>
</template>

<script>
import ExcelDataPreparatory from '../common/ExcelDataPreparatory.vue'
import TableCardProps from '../common/TableCardProps.vue'
import {hideBusySpinner, showBusySpinner} from "../../tools";

export default {
    name: "StoredItemInfoTable",
    mixins: [ExcelDataPreparatory, TableCardProps],
    mounted() {
        if (this.columnsToHide)
            this.hideColumns();
        this.setItems();
        if (!this.preventItemLoading)
            this.getItems();
    },
    props: {
        branches: {
            type: Array
        },
        groupByBranch: {
            type: Boolean,
            default: true
        },
        url: {
            type: String,
            default: 'stored-item-info/filtered?'
        },
        providedStoredItems: {
            type: Array
        },
        providedSelectedStoredItems: {
            type: Array
        },
        preventItemLoading: {
            type: Boolean,
            default: false
        },
        columnsToHide: {
            type: Array
        },
        prepareUrl: {
            type: Function,
            default: (vm) => {
                let action = vm.url;
                if (vm.selectedBranch)
                    action = `${vm.url}branch=${vm.selectedBranch.id}&`;
                return action;
            }
        },
        flowable: false
    },
    data() {
        return {
            selectedBranch: null,
            pagination: null,
            isBusy: false,
            items: [],
            fields: [
                {
                    key: 'created_at',
                    label: 'Дaта',
                    sortable: true
                },
                {
                    key: 'tariff.name',
                    label: 'Тариф',
                    sortable: true
                },
                {
                    key: 'owner.code',
                    label: 'Владелец',
                    sortable: true
                },
                {
                    key: 'shop',
                    label: 'Магазин',
                    sortable: true
                },
                {
                    key: 'item.name',
                    label: 'Наименование',
                    sortable: true
                },
                {
                    key: 'weight',
                    label: 'Вес',
                    sortable: true
                },
                {
                    key: 'width',
                    label: 'Ширина',
                    sortable: true
                },
                {
                    key: 'height',
                    label: 'Высота',
                    sortable: true
                },
                {
                    key: 'length',
                    label: 'Длина',
                    sortable: true
                },
                {
                    key: 'cubage',
                    label: 'Кубатура',
                    sortable: true
                },
                {
                    key: 'groupedStoredItemsCount',
                    label: 'Количество',
                    sortable: true
                },
                {
                    key: 'selectedCount',
                    label: 'Кол-во мест'
                },
                {
                    key: 'totalCubage',
                    label: 'Общ. Кубатура',
                    sortable: true
                },
                {
                    key: 'totalWeight',
                    label: 'Общ. Вес',
                    sortable: true
                },
                {
                    key: 'weightPerCube',
                    label: 'Кг в 1 кубе',
                    sortable: true
                },
                {
                    key: 'totalPrice',
                    label: 'Сумма',
                    sortable: true
                },
                {
                    key: 'groupedStoredItemsStorage.name',
                    label: 'Склад',
                    sortable: true
                },
                {
                    key: 'edit',
                    label: ''
                }
            ],

            compensation: 0,
            lostItem: null,
            lostStoredItemsCount: 0
        }
    },
    methods: {
        hideColumns() {
            this.fields = this.fields.filter(f => !this.columnsToHide.includes(f.key));
        },
        getCubage(item) {
            if (item.type === 'dummy')
                return item.cubage;
            return item.cubage = Math.round(item.length * item.width * item.height * 1000) / 1000
        },
        getTotalCubage(item) {
            if (item.type === 'dummy')
                return item.totalCubage;
            return item.totalCubage = Math.round(item.cubage * item.groupedStoredItemsCount * 1000) / 1000
        },
        getTotalWeight(item) {
            if (item.type === 'dummy')
                return item.totalWeight;
            return item.totalWeight = Math.round(item.weight * item.groupedStoredItemsCount * 1000) / 1000
        },
        getWeightPerCube(item) {
            if (item.type === 'dummy')
                return item.weightPerCube;
            if (item.totalCubage > 0)
                return item.weightPerCube = Math.round(item.totalWeight / item.totalCubage * 1000) / 1000
        },
        getItemsLength(item) {
            if (item.type === 'dummy')
                return item.groupedStoredItemsCount;
            this.$set(item, 'groupedStoredItemsCount', item.storedItems.length);
            return item.groupedStoredItemsCount;
        },
        getTotalPrice(item) {
            console.log(item.billingInfo)
            if (item.type === 'dummy')
                return item.totalPrice;
            if (item.billingInfo)
                return item.totalPrice = Math.round(item.billingInfo.pricePerItem * item.groupedStoredItemsCount * 100) / 100;
        },
        setRowClass(item, type) {
            if (item && item.primaryKey === 'dummyTotalStatItem')
                return 'table-success';
            if (item && item.primaryKey === 'dummyOldStatItem')
                return 'table-warning';
        },
        //Converts provided storedItem to StoredItemInfos
        convertStoredItemsToInfos(storedItems) {
            let groupedStoredItemsByInfo = storedItems.reduce((r, stored) => {
                r[stored.info.id] = [...r[stored.info.id] || [], stored];
                return r;
            }, {});
            //infos
            return Object.keys(groupedStoredItemsByInfo).map((key) => {
                let storedItemInfo = Object.assign({}, groupedStoredItemsByInfo[key][0].info);
                storedItemInfo.storedItems = groupedStoredItemsByInfo[key].map((stored) => {
                    let storedCopy = Object.assign({}, stored);
                    delete storedCopy.info;
                    return storedCopy;
                });

                return storedItemInfo;
            });
        },
        //Groups stored item infos by storage
        groupStoredItemInfosByBranch(infos, countStoredItems = false) {
            let storedItemInfos = [];
            let index = 0;

            infos.forEach(info => {
                let groupedStoredItems = info.storedItems.reduce((r, stored) => {
                    if (!stored.storageHistory)
                        stored.storageHistory = {storage: {id: null}};
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
                    // storedItemInfo.selectedCount = countStoredItems ? storedItemInfo.storedItems.length : 0;
                    storedItemInfo.selectedCount = countStoredItems ? this.countSelectedStoredItemsForInfo(storedItemInfo) : 0;
                    return storedItemInfo;
                });

                storedItemInfos = [...storedItemInfos, ...infos]
            });

            return storedItemInfos;
        },
        countSelectedStoredItemsForInfo(storedItemInfo) {
            let count = 0;
            storedItemInfo.storedItems.forEach(storedItem => {
                    if (this.isInProvidedSelectedItems(storedItem))
                        count++
                }
            )

            return count;
        },
        isInProvidedSelectedItems(storedItem) {
            return this.providedSelectedStoredItems.find(function (selectedStoredItem) {
                return selectedStoredItem.id === storedItem.id;
            });
        },
        setItems() {
            if (this.providedStoredItems) {
                let storedItemInfos = this.convertStoredItemsToInfos(this.providedStoredItems);
                let storedItems = this.groupStoredItemInfosByBranch(storedItemInfos, true);
                for (let item of storedItems) {
                    this.items.push(item);
                }
            }

            this.updateSelectedStoredItems();
        },
        async getItems(page = 1) {
            this.isBusy = true;
            showBusySpinner()

            let action = this.prepareUrl(this);

            return axios.get(action + 'paginate=10&page=' + page)
                .then(response => {
                    this.pagination = response.data;

                    let items = [];

                    if (this.groupByBranch) {
                        let storedItemInfos = this.groupStoredItemInfosByBranch(response.data.data);
                        items = storedItemInfos.filter(item => {
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
                    } else {
                        items = response.data.data.filter(item => item.storedItems.length > 0);
                    }


                    if (this.flowable)
                        items.forEach(item => {
                            this.items.push(item);
                        });
                    else {
                        this.items = [...this.items, ...items];
                    }
                    this.$nextTick(() => {
                        this.isBusy = false;
                    });
                })
                .catch(e => console.log(e))
                .then(_ => hideBusySpinner());
        },
        // Checks if item in all items array
        findInItems(item) {
            return this.items.find(function (stored) {
                return stored.primaryKey === item.primaryKey;
            });
        },
        updateSelectedStoredItems(fireEvent = true) {
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
            if (fireEvent)
                this.$emit('onItemsSelected', selectedItems);
        },
    },
    watch: {
        selectedBranch() {
            if (!this.preventItemLoading) {
                this.items.splice(0, this.items.length);
                this.setItems();
                this.getItems();
            }

            this.$emit('branchSelected', this.selectedBranch);
        },
        providedStoredItems() {
            this.items.splice(0, this.items.length);
            this.setItems();
            if (!this.preventItemLoading)
                this.getItems();
        },
        providedSelectedStoredItems() {
            this.items.forEach(function (storedItemInfo) {
                storedItemInfo.selectedCount = 0;

                storedItemInfo.storedItems.forEach(function (storedItem) {
                    let exists = this.providedSelectedStoredItems.find(provided => provided.id === storedItem.id);
                    if (exists) storedItemInfo.selectedCount++;
                }, this);
            }, this);

            this.updateSelectedStoredItems(false);
        }
    },
    components: {
        'TableCard':
        require('../common/TableCard.vue').default
    }
}
</script>
