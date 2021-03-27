<template xmlns:v-slot="http://www.w3.org/1999/XSL/Transform">
    <div class="card">
        <div class="card-header">
            <div class="row align-items-baseline">
                <slot name="header">

                </slot>
                <div class="ml-md-auto d-flex" v-if="trip">
                    <a class="btn btn-primary mr-md-1" :href="`/trip/${trip.id}/customs-report`">Декларация</a>
                    <select class="form-control custom-select mr-md-1">
                        <option :value="null">--Статус--</option>
                        <option value="listed">Добавлен в предварительный список</option>
                        <option value="abandoned">Не был загружен в машину</option>
                        <option value="loaded">Загружен в машину</option>
                        <option value="completed">Завершен</option>
                        <option value="canceled">Удален</option>
                    </select>
                    <select class="form-control custom-select" id="branch" v-model="selectedClient">
                        <option :value="null">--Все клиенты--</option>
                        <option :key="client.id" :value="client" v-for="client in clients">
                            {{ client.code }}
                        </option>
                    </select>
                </div>
                <div class="ml-md-3">
                    <vue-excel-xlsx
                        :columns="excelColumns"
                        :data="excelData"
                        sheetname="Лист 1"
                        class="btn"
                        filename="Список товаров">
                        <img class="icon-btn-md" src="/svg/excel.svg">
                    </vue-excel-xlsx>
                </div>
            </div>
        </div>

        <b-table :busy="isBusy"
                 :fields="fields"
                 :items="items"
                 :striped="true"
                 borderless
                 primary-key="id"
                 responsive
                 select-mode="single">
            <template v-slot:table-busy>
                <div class="text-center text-info my-2">
                    <b-spinner class="align-middle"></b-spinner>
                </div>
            </template>

            <template v-slot:cell(owner)="{item}">
                <span>{{ item.info.owner.code }} {{ item.info.owner.name }}</span>
            </template>

            <template v-slot:cell(info.weight)="{item}">
                <span>{{ item.info.weight.toFixed(3) }}</span>
            </template>

            <template v-slot:cell(info.height)="{item}">
                <span>{{ item.info.height.toFixed(3) }}</span>
            </template>

            <template v-slot:cell(info.length)="{item}">
                <span>{{ item.info.length.toFixed(3) }}</span>
            </template>

            <template v-slot:cell(info.width)="{item}">
                <span>{{ item.info.width.toFixed(3) }}</span>
            </template>

            <template v-slot:cell(info.cubage)="{item}">
                <span>{{ calculateCubage(item.info).toFixed(3) }}</span>
            </template>

            <template v-slot:cell(tripHistory.status)="{item}">
                <template v-if="item.tripHistory">
                    <div v-if="item.tripHistory.status === 'listed'" class="table-warning p-2 rounded text-center">
                        Добавлен в редварительный список
                    </div>
                    <div v-if="item.tripHistory.status === 'abandoned'" class="table-danger p-2 rounded text-center">
                        Не был загружен в машину
                    </div>
                    <div v-if="item.tripHistory.status === 'loaded'" class="table-primary p-2 rounded text-center">
                        Загружен в машину
                    </div>
                    <div v-if="item.tripHistory.status === 'completed'" class="table-success p-2 rounded text-center">
                        Завершен
                    </div>
                    <div v-if="item.tripHistory.status === 'canceled'" class="table-secondary p-2 rounded text-center">
                        Удален
                    </div>
                </template>
            </template>
        </b-table>
        <vue-excel-xlsx
            id="grouped-data"
            :columns="groupedDataColumns"
            :data="groupedData"
            sheetname="Лист 1"
            class="btn"
            filename="Таможенные выплаты">
        </vue-excel-xlsx>
    </div>
</template>

<script>
import ExcelDataPreparatory from '../common/ExcelDataPreparatory.vue'

export default {
    name: "SortableStoredTable",
    mixins: [ExcelDataPreparatory],
    mounted() {
        this.items = this.storedItems;
        this.extractClients();
        this.calculateDutyPrices();

        if (this.trip)
            this.fields.push({
                key: 'tripHistory.status',
                label: 'Статус товара'
            })
    },
    props: {
        storedItems: {
            type: Array,
            required: true
        },
        trip: {
            type: Object,
            required: true
        }
    },
    methods: {
        extractClients() {
            this.storedItems.forEach((item) => {
                if (!this.clients.find(x => x.id === item.info.owner.id)) {
                    this.clients.push(item.info.owner);
                }
            }, this);
        },
        // groupByTariffAndCode() {
        //     let groupedByTariff = groupBy(this.items, i => i.info.tariff.id);
        //     let groupedByCode = [];
        //
        //     groupedByTariff.forEach((arr) => {
        //         let grouped = groupBy(arr, i => i.info.customsCode.id);
        //         grouped.forEach((arr) => {
        //             groupedByCode.push(...arr);
        //         })
        //     });
        //
        //     this.items = groupedByCode;
        //     this.fillGroupedData();
        //     $('#grouped-data').click();
        // },
        fillGroupedData() {
            let groupedByCode = groupBy(this.items, i => i.info.customsCode.id);
            groupedByCode.forEach((arr) => {
                let totalDutyPrice = 0;
                let totalWeight = 0;
                let count = 0;
                let totalPrice = 0;

                arr.forEach((item) => {
                    totalDutyPrice += item.dutyPrice;
                    totalWeight += item.info.weight;
                    totalPrice += item.info.billingInfo.pricePerItem;
                    count++;
                });

                let customsCode = arr[0].info.customsCode;

                this.groupedData.push({
                    name: customsCode.name,
                    code: customsCode.code,
                    count: count,
                    totalWeight: totalWeight,
                    totalPrice: totalPrice,
                    averagePricePerItem: Math.round(totalPrice / count * 100) / 100,
                    totalDutyPrice: totalDutyPrice
                });
            });
        },
        calculateDutyPrices() {
            this.items.forEach((item) => {
                let dutyPrice = 0;
                let customsTariff = item.info.customsCode;
                if (customsTariff.isCalculatedByPiece) {
                    dutyPrice = customsTariff.price * customsTariff.totalRate / 100
                } else {
                    let tonnage = item.info.weight / 1000;
                    dutyPrice = tonnage * customsTariff.price * customsTariff.totalRate / 100
                }
                item.dutyPrice = Math.round(dutyPrice * 100) / 100;
            })
        },
        calculateCubage(info) {
            return info.cubage = Math.round(info.height * info.width * info.length * 100) / 100;
        }
    },
    watch: {
        selectedClient() {
            if (this.selectedClient)
                this.items = this.storedItems.filter((item) => {
                    return item.info.owner.id === this.selectedClient.id
                }, this);
            else
                this.items = this.storedItems;
        }
    },
    data() {
        return {
            items: [],
            excelColumns: [],
            excelData: [],
            isBusy: false,
            selectedClient: null,
            clients: [],
            groupedData: [],
            fields: [
                {
                    key: 'owner',
                    label: 'Владелец',
                    sortable: true
                },
                {
                    key: 'info.item.name',
                    label: 'Наименование',
                    sortable: true
                },
                {
                    key: 'code',
                    label: 'Код',
                    sortable: true
                },
                {
                    key: 'info.tariff.name',
                    label: 'Тариф',
                    sortable: true
                },
                {
                    key: 'info.customsCode.code',
                    label: 'Там. код',
                    sortable: true
                },
                {
                    key: 'info.width',
                    label: 'Ширина',
                    sortable: true
                },
                {
                    key: 'info.height',
                    label: 'Высота',
                    sortable: true
                },
                {
                    key: 'info.length',
                    label: 'Длина',
                    sortable: true
                },
                {
                    key: 'info.cubage',
                    label: 'Кубатура',
                    sortable: true
                },
                {
                    key: 'info.weight',
                    label: 'Вес',
                    sortable: true
                },
                {
                    key: 'storageHistory.storage.name',
                    label: 'Склад'
                },
                // {
                //     key: 'dutyPrice',
                //     label: 'Таможенная пошлина'
                // }
                //In case of trip added in mount
                // {
                //     key: 'tripHistory.status',
                //     label: 'Статус товара'
                // }
            ],
            groupedDataColumns: [
                {
                    label: 'Наименование',
                    field: 'name'
                },
                {
                    label: 'Код',
                    field: 'code'
                },
                {
                    label: 'Количество',
                    field: 'count'
                },
                {
                    label: 'Вес',
                    field: 'totalWeight'
                },
                {
                    label: 'Средняя цена за штуку',
                    field: 'averagePricePerItem'
                },
                {
                    label: 'Общая стоимость',
                    field: 'totalPrice'
                },
                {
                    label: 'Таможенная пошлина',
                    field: 'totalDutyPrice'
                }
            ]
        }
    },
    components: {
        'StoredTable': require('./StoredTable.vue').default
    },
}
</script>
