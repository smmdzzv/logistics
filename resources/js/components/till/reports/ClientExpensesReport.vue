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
            <table-card :excelFileName="`Акт сверки от ${dateFrom} клиента ${client.code}`"
                        :excelSheetName="`${client.code}  ${dateFrom}`"
                        primary-key="date"
                        :fields="fields"
                        :items="reportData"
                        :setRowClass="setRowClass"
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
                client: {code: ''},
                reportData: [],
                fields: [
                    {
                        key: 'date',
                        label: 'Дата'
                    },
                    {
                        key: 'in.amount',
                        label: 'Пост. сумма'
                    },
                    {
                        key: 'in.placesCount',
                        label: 'Пост. кол-во мест'
                    },
                    {
                        key: 'in.discount',
                        label: 'Скидка'
                    },
                    {
                        key: 'out.amount',
                        label: 'Оплата сумма'
                    },
                    {
                        key: 'out.placesCount',
                        label: 'Оплата кол-во мест'
                    },
                    {
                        key: 'placesLeft',
                        label: 'Остаток кол-ва мест'
                    },
                    {
                        key: 'debt',
                        label: 'Задолженность'
                    }
                ]
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
                        date: DateTime.fromISO(data.reportData[i].date).setZone('utc').toFormat('dd-MM-yyyy'),
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

                for (let i = 0; i < objects.length; i++) {
                    let prevDebt = 0;
                    let prevPlacesLeft = 0;
                    if (i === 0) {
                        prevPlacesLeft = data.placesCountAtStart;
                        prevDebt = data.debtAtStart;
                    } else {
                        prevPlacesLeft = objects[i - 1].placesLeft;
                        prevDebt = objects[i - 1].debt;
                    }


                    objects[i].debt = Math.round((prevDebt + objects[i].in.amount - objects[i].out.amount) * 100) / 100;
                    objects[i].placesLeft = prevPlacesLeft + objects[i].in.placesCount - objects[i].out.placesCount;
                }

                this.reportData = objects;

                this.insertDummyItems(data);
            },
            // filterDummyItems(){
            //     this.reportData = this.reportData.filter(data => data.date !== 'Итого');
            // },
            insertDummyItems(data) {
                // this.filterDummyItems();

                let total = {
                    out: {amount: 0, placesCount: 0, discount: 0},
                    in: {amount: 0, placesCount: 0, discount: 0}
                };

                for (let i = 0; i < this.reportData.length; i++) {
                    total.date = 'Итого';
                    total.out.amount += this.reportData[i].out.amount;
                    total.out.placesCount += this.reportData[i].out.placesCount;
                    total.out.discount += this.reportData[i].out.discount;
                    total.in.amount += this.reportData[i].in.amount;
                    total.in.placesCount += this.reportData[i].in.placesCount;
                    total.in.discount += this.reportData[i].in.discount;
                }

                let previous = {
                    date: 'До начала периода',
                    in: {amount: data.debtAtStart, placesCount: data.placesCountAtStart, discount: null},
                    out: {amount: null, placesCount: null, discount: null}
                };
                this.reportData.unshift(previous);

                this.reportData.push(total);
            },
            setRowClass(item, type) {
                if (item && item.date === 'Итого')
                    return 'table-success';
                if (item && item.date === 'До начала периода')
                    return 'table-warning';
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
