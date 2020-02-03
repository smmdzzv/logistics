<template xmlns:v-slot="http://www.w3.org/1999/XSL/Transform">
    <div class="card">
        <div class="card-header">
            <div class="row align-items-baseline">
                <slot name="header">

                </slot>
                <div class="ml-md-auto" v-if="clients">
                    <button class="btn btn-link" @click="groupByTariffAndCode">Группировать по коду</button>
                </div>
                <div class="ml-md-2">
                    <select class="form-control custom-select" id="branch" v-model="selectedClient">
                        <option :value="null">--Все клиенты--</option>
                        <option :key="client.id" :value="client" v-for="client in clients">
                            {{client.name}}
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
                 select-mode="single"
                 sticky-header="400px">
            <template v-slot:table-busy>
                <div class="text-center text-info my-2">
                    <b-spinner class="align-middle"></b-spinner>
                </div>
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
        },
        props: {
            storedItems: {
                type: Array,
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
            groupByTariffAndCode() {
                let groupedByTariff = groupBy(this.items, i => i.info.item.tariff.id);
                let groupedByCode = [];

                groupedByTariff.forEach((arr) => {
                    let grouped = groupBy(arr, i => i.info.customsCode.id);
                    grouped.forEach((arr) => {
                        groupedByCode.push(...arr);
                    })
                });

                this.items = groupedByCode;
                this.fillGroupedData();
                $('#grouped-data').click();
            },
            fillGroupedData(){
                let groupedByCode = groupBy(this.items, i => i.info.customsCode.id);
                groupedByCode.forEach((arr) => {
                    let totalDutyPrice = 0;
                    let totalWeight = 0;
                    arr.forEach((item) => {
                        totalDutyPrice += item.dutyPrice;
                        totalWeight += item.info.weight;
                    });

                    let customsCode = arr[0].info.customsCode;

                    this.groupedData.push({name: customsCode.name, code: customsCode.code, weight: totalWeight, dutyPrice: totalDutyPrice});
                });
            },
            calculateDutyPrices(){
                this.items.forEach((item)=> {
                    let dutyPrice = 0;
                    let customsTariff = item.info.customsCode;
                    if(customsTariff.isCalculatedByPiece){
                        dutyPrice = customsTariff.price * customsTariff.totalRate / 100
                    }
                    else{
                        let tonnage = item.info.weight / 1000;
                        dutyPrice = tonnage * customsTariff.price * customsTariff.totalRate / 100
                    }
                    item.dutyPrice = Math.round(dutyPrice * 100) / 100;
                })
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
                groupedData:[],
                fields: {
                    'info.item.name': {
                        label: 'Наименование',
                        sortable: true
                    },
                    code: {
                        label: 'Код',
                        sortable: true
                    },
                    'info.item.tariff.name': {
                        label: 'Тариф',
                        sortable: true
                    },
                    'info.customsCode.code': {
                        label: 'Там. код',
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
                    'storageHistory.storage.name': {
                        label: 'Склад'
                    },
                    'dutyPrice':{
                        label: 'Таможенная пошлина'
                    }
                },
                groupedDataColumns:[
                   {
                        label: 'Наименование',
                        field: 'name'
                    },
                    {
                        label: 'Код',
                        field: 'code'
                    },
                    {
                        label: 'Вес',
                        field: 'weight'
                    },
                    {
                        label: 'Таможенная пошлина',
                        field: 'dutyPrice'
                    }
                ]
            }
        },
        components: {
            'StoredTable': require('./StoredTable.vue').default
        },
    }
</script>
