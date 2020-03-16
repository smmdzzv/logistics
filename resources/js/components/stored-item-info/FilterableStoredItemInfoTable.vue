<template>
    <div>
        <div class="alert alert-secondary">
            <div class="form-row align-items-baseline">
                <div class="form-group col-md-2">
                    <label for="clientCode">Код клиента</label>
                    <input id="clientCode" type="text" v-model.lazy="clientCode" class="form-control form-control-sm">
                </div>
                <div class="form-group col-md-2">
                    <label for="shop">Магазин</label>
                    <input id="shop" type="text" v-model.lazy="shop" class="form-control form-control-sm">
                </div>
                <div class="form-group col-md-2">
                    <label for="minCubage">Мин кубатура</label>
                    <input id="minCubage" type="number" v-model.lazy="minCubage" class="form-control form-control-sm">
                </div>
                <div class="form-group col-md-2">
                    <label for="maxCubage">Макс кубатура</label>
                    <input id="maxCubage" type="number" v-model.lazy="maxCubage" class="form-control form-control-sm">
                </div>
                <div class="form-group col-md-2">
                    <label for="minWeight">Мин вес</label>
                    <input id="minWeight" type="number" v-model.lazy="minWeight" class="form-control form-control-sm">
                </div>
                <div class="form-group col-md-2">
                    <label for="maxWeight">Макс вес</label>
                    <input id="maxWeight" type="number" v-model.lazy="maxWeight" class="form-control form-control-sm">
                </div>
                <div class="form-group col-md-2">
                    <label for="minPrice">Мин цена</label>
                    <input id="minPrice" type="number" v-model.lazy="minPrice" class="form-control form-control-sm">
                </div>
                <div class="form-group col-md-2">
                    <label for="maxPrice">Макс цена</label>
                    <input id="maxPrice" type="number" v-model.lazy="maxPrice" class="form-control form-control-sm">
                </div>
                <div class="form-group col-md-2">
                    <label for="dateFrom">От</label>
                    <input id="dateFrom" type="date" v-model.lazy="dateFrom" class="form-control form-control-sm">
                </div>
                <div class="form-group col-md-2">
                    <label for="dateTo">До</label>
                    <input id="dateTo" type="date" v-model.lazy="dateTo" class="form-control form-control-sm">
                </div>
                <div class="form-group col-md-2">
                    <label for="status">Статус</label>
                    <select id="status" v-model.lazy="status" class="form-control form-control-sm">
                        <option :value="null">--Любые--</option>
                        <option value="accepted">Активные</option>
                        <option value="completed">Выданные</option>
                    </select>
                </div>
                <div class="form-group col-md-2">
                    <label for="trip">Рейс</label>
                    <select id="trip" v-model.lazy="trip" class="form-control form-control-sm">
                        <option :value="null">--Любые--</option>
                        <option value="hasTrip">С рейсом</option>
                        <option value="doesntHaveTrip">Без рейса</option>
                    </select>
                </div>
                <div class="form-group col-md-2">
                    <label>Наименования</label>
                    <select class="form-control form-control-sm" id="item" v-model="item">
                        <option :value="null">--Все склады--</option>
                        <option :key="item.id" :value="item.id" v-for="item in items">
                            {{item.name}}
                        </option>
                    </select>
                </div>
                <div class="form-group col-md-2">
                    <label>Филиалы</label>
                    <select class="form-control form-control-sm" id="branch" v-model="branch">
                        <option :value="null">--Все товары--</option>
                        <option :key="branch.id" :value="branch.id" v-for="branch in branches">
                            {{branch.name}}
                        </option>
                    </select>
                </div>
                <div class="form-group col-md-4">
                    <label/>
                    <b-input-group>
                        <b-input-group-prepend is-text>
                            <b-form-checkbox switch class="mr-n2"
                                             v-model="groupByBranch"
                                             name="groupByBranch">
                            </b-form-checkbox>
                        </b-input-group-prepend>
                        <b-form-input disabled value="Группировать по складу"></b-form-input>
                    </b-input-group>
                </div>
            </div>
            <div class="row">
                <button class="btn btn-primary mx-auto" @click="fetchData">Загрузить</button>
            </div>
        </div>
        <StoredItemInfoTable :columnsToHide="columnsToHide"
                             flowable
                             :groupByBranch="groupByBranch"
                             :excelSheetName="excelSheetName"
                             :prepareUrl="prepareUrl"
                             ref="storedItemInfosTable"/>
    </div>
</template>

<script>
    export default {
        name: "FilterableStoredItemInfoTable",
        props: {
            columnsToHide: {
                default: () => []
            },
            branches: {
                required: true
            },
            items: {
                required: true
            }
        },
        data() {
            return {
                clientCode: null,
                shop: null,
                employeeCode: null,
                minCubage: null,
                maxCubage: null,
                minWeight: null,
                maxWeight: null,
                minPrice: null,
                maxPrice: null,
                dateFrom: null,
                dateTo: null,
                status: null,
                trip: null,
                branch: null,
                item: null,
                groupByBranch: true,
                actionUrl: 'stored-item-info/filtered?'
            }
        },
        methods: {
            fetchData() {
                this.$refs.storedItemInfosTable.items = [];
                this.$refs.storedItemInfosTable.getItems()
                    .then(_ => {
                        this.calculateTotalStat();

                        if (this.clientCode && this.dateFrom)
                            this.fetchClientStat();
                    });

            },
            fetchClientStat() {
                let action = this.prepareUrl(null, `/stored-item-info/statistics?`);
                axios.get(action).then(res => {
                    let dummyOldStatItem = res.data;
                    dummyOldStatItem.type = 'dummy';
                    dummyOldStatItem.primaryKey = 'dummyOldStatItem';
                    dummyOldStatItem.weightPerCube = dummyOldStatItem.averageWeightPerCube;
                    this.$refs.storedItemInfosTable.items
                        = this.$refs.storedItemInfosTable.items.filter((info) => info.primaryKey !== 'dummyOldStatItem');
                    console.log(dummyOldStatItem);
                    this.$refs.storedItemInfosTable.items.unshift(dummyOldStatItem)
                })
            },
            calculateTotalStat() {
                let dummyTotalStatItem = {
                    primaryKey: "dummyTotalStatItem",
                    groupedStoredItemsCount: 0,
                    type: 'dummy',
                    totalCubage: 0,
                    totalWeight: 0,
                    weightPerCube: 0,
                    totalPrice: 0
                };

                for (let i = 0; i < this.$refs.storedItemInfosTable.items.length; i++) {
                    dummyTotalStatItem.groupedStoredItemsCount += this.$refs.storedItemInfosTable.items[i].storedItems.length;
                    dummyTotalStatItem.totalCubage += this.$refs.storedItemInfosTable.items[i].totalCubage;
                    dummyTotalStatItem.totalWeight += this.$refs.storedItemInfosTable.items[i].totalWeight;
                    dummyTotalStatItem.totalPrice +=
                        this.$refs.storedItemInfosTable.items[i].billingInfo.pricePerItem
                        * this.$refs.storedItemInfosTable.items[i].groupedStoredItemsCount;
                    dummyTotalStatItem.weightPerCube += this.$refs.storedItemInfosTable.items[i].weightPerCube;
                }

                dummyTotalStatItem.totalCubage = Math.round(dummyTotalStatItem.totalCubage * 1000) / 1000;
                dummyTotalStatItem.totalWeight = Math.round(dummyTotalStatItem.totalWeight * 1000) / 1000;
                if (this.$refs.storedItemInfosTable.items.length > 0)
                    dummyTotalStatItem.weightPerCube
                        = Math.round(dummyTotalStatItem.weightPerCube / this.$refs.storedItemInfosTable.items.length * 1000) / 1000;
                else
                    dummyTotalStatItem.weightPerCube = null;

                this.$refs.storedItemInfosTable.items
                    = this.$refs.storedItemInfosTable.items.filter((info) => info.primaryKey !== 'dummyTotalStatItem');

                this.$refs.storedItemInfosTable.items.unshift(dummyTotalStatItem)
            },
            prepareUrl(vm, url = null) {
                let actionUrl = url ? url : '/stored-item-info/filtered?';
                if (this.clientCode)
                    actionUrl += `client=${this.clientCode}&`;
                if (this.minCubage)
                    actionUrl += `minCubage=${this.minCubage}&`;
                if (this.maxCubage)
                    actionUrl += `maxCubage=${this.maxCubage}&`;
                if (this.minWeight)
                    actionUrl += `minWeight=${this.minWeight}&`;
                if (this.maxWeight)
                    actionUrl += `maxWeight=${this.maxWeight}&`;
                if (this.minPrice)
                    actionUrl += `minPrice=${this.minPrice}&`;
                if (this.maxPrice)
                    actionUrl += `maxPrice=${this.maxPrice}&`;
                if (this.dateFrom)
                    actionUrl += `dateFrom=${this.dateFrom}&`;
                if (this.dateTo)
                    actionUrl += `dateTo=${this.dateTo}&`;
                if (this.status)
                    actionUrl += `status=${this.status}&`;
                if (this.branch)
                    actionUrl += `branch=${this.branch}&`;
                if (this.trip)
                    actionUrl += `trip=${this.trip}&`;
                if (this.item)
                    actionUrl += `item=${this.item}&`;

                return actionUrl;
            },
        },
        computed: {
            excelSheetName() {
                let name = "Лист 1";
                if (this.clientCode)
                    name = this.clientCode;
                return name;
            }
        },
        components: {
            'StoredItemInfoTable': require('./StoredItemInfoTable').default
        }
    }
</script>

<style scoped>

</style>
