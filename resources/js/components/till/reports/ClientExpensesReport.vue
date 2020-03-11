<template>
    <div>
        <div class="row px-2">
            <div class="form-group col-md-4">
                <label>Клиент</label>
                <search-user-dropdown url="/concrete/client/filter?userInfo="
                                      :selected="onClientSelected"
                                      autofocus></search-user-dropdown>
            </div>
            <div class="form-group col-md-4">
                <label>Дата начала</label>
                <input class="form-control" type="date" v-model="dateFrom">
            </div>
            <div class="form-group col-md-4">
                <label>Дата конца</label>
                <input class="form-control" type="date" v-model="dateTo">
            </div>
        </div>
        <div class="row mb-5">
            <button class="mx-auto btn btn-primary" @click="fetchData">Сформировать отчет</button>
        </div>
        <div class="col-12">
            <table-card excelFileName="Акт сверки"
                        primary-key="date"
                        :fields="fields"
                        :items="reportData"
                        responsive>
                <template #header>
                    Акт Сверки
                </template>

                <template slot="type" slot-scope="{item}">
                    <span v-if="item.type === 'in'">Приход</span>
                    <span v-if="item.type === 'out'">Расход</span>
                </template>
            </table-card>
        </div>
    </div>
</template>

<script>
    import {hideBusySpinner, showBusySpinner} from "../../../tools";
    import {DateTime} from 'luxon';

    export default {
        name: "ClientExpensesReport",
        data() {
            return {
                dateFrom: null,
                dateTo: null,
                client: null,
                reportData: [],
                fields: {
                    date: {
                        label: 'Дата'
                    },
                    'in.amount': {
                        label: 'Пост. сумма'
                    },
                    'in.placesCount': {
                        label: 'Пост. кол-во мест'
                    },
                    'in.discount': {
                        label: 'Скидка'
                    },
                    'out.amount': {
                        label: 'Оплата сумма'
                    },
                    'out.placesCount': {
                        label: 'Оплата кол-во мест'
                    }
                }
            }
        },
        methods: {
            onClientSelected(client) {
                this.client = client;
            },
            fetchData: function () {
                if (!this.dateFrom || !this.client)
                    return;

                showBusySpinner();

                let action = `/reports/expenses/generate?client=${this.client.id}&dateFrom=${this.dateFrom}`;
                if (this.dateTo)
                    action += `&dateTo=${this.dateTo}`;

                axios.get(action)
                    .then(response => this.setReportData(response.data))
                    .catch(e => console.log(e))
                    .then(_ => hideBusySpinner())

            },
            setReportData(data) {
                let objects = [];

                for (let i = 0; i < data.reportData.length; i++) {
                    let obj = {
                        date: DateTime.fromISO(data.reportData[i].date).toFormat('dd-MM-yyyy'),
                        out: {amount: 0, placesCount: 0, discount: 0},
                        in: {amount: 0, placesCount: 0, discount: 0}
                    };

                    let key = data.reportData[i].type;
                    obj[key] = data.reportData[i];

                    let existing = objects.find(item => item.date === obj.date);
                    if (existing) {
                        existing[key].amount += obj[key].amount;
                        existing[key].placesCount += obj[key].placesCount;
                        existing[key].discount += obj[key].discount;
                    } else objects.push(obj);
                }

                this.reportData = objects;
            }
        },
        components: {
            'SearchUserDropdown': require('../../users/SearchUserDropdown.vue').default,
            'TableCard': require('../../common/TableCard.vue').default
        }
    }
</script>

<style scoped>

</style>
