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
                    <label for="employeeCode">Код сотрудника</label>
                    <input id="employeeCode" type="text" v-model.lazy="employeeCode"
                           class="form-control form-control-sm">
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
                        <option value="accepted">С рейсом</option>
                        <option value="completed">Без рейса</option>
                    </select>
                </div>
                <div class="form-group col-md-2">
                    <label>Филиалы</label>
                    <select class="form-control form-control-sm" id="branch" v-model="branch">
                        <option :value="null">--Все склады--</option>
                        <option :key="branch.id" :value="branch" v-for="branch in branches">
                            {{branch.name}}
                        </option>
                    </select>
                </div>
            </div>
            <div class="row">
                <button class="btn btn-primary mx-auto" @click="fetchData">Загрузить</button>
            </div>
        </div>
        <StoredItemInfoTable :columnsToHide="columnsToHide" :url="actionUrl" ref="storedItemInfosTable"/>
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
                actionUrl: 'stored-item-info/available/filtered?'
            }
        },
        methods: {
            fetchData() {
                this.prepareUrl();
                this.$refs.storedItemInfosTable.getItems();
            },
            prepareUrl() {
                this.actionUrl = '/stored-item-info/available/filtered?';
                if (this.clientCode)
                    this.actionUrl += `client=${this.clientCode}&`;
                if (this.employeeCode)
                    this.actionUrl += `employee=${this.employeeCode}&`;
                if (this.minCubage)
                    this.actionUrl += `minCubage=${this.minCubage}&`;
                if (this.maxCubage)
                    this.actionUrl += `maxCubage=${this.maxCubage}&`;
                if (this.minWeight)
                    this.actionUrl += `minWeight=${this.minWeight}&`;
                if (this.maxWeight)
                    this.actionUrl += `maxWeight=${this.maxWeight}&`;
                if (this.minPrice)
                    this.actionUrl += `minPrice=${this.minPrice}&`;
                if (this.maxPrice)
                    this.actionUrl += `maxPrice=${this.maxPrice}&`;
                if (this.dateFrom)
                    this.actionUrl += `dateFrom=${this.dateFrom}&`;
                if (this.dateTo)
                    this.actionUrl += `dateTo=${this.dateTo}&`;
                if (this.status)
                    this.actionUrl += `status=${this.status}&`;
                if (this.branch)
                    this.actionUrl += `status=${this.branch}&`;
            }
        },
        components: {
            'StoredItemInfoTable': require('./StoredItemInfoTable').default
        }
    }
</script>

<style scoped>

</style>
