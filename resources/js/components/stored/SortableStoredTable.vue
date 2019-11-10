<template>
    <div class="card">
        <div class="card-header">
            <div class="row align-items-baseline">
                <slot name="header">

                </slot>
                <div class="ml-md-auto" v-if="clients">
                    <select class="form-control custom-select" id="branch" v-model="selectedClient">
                        <option :value="null">--Все клиенты--</option>
                        <option :key="client.id" :value="client" v-for="client in clients">
                            {{client.name}}
                        </option>
                    </select>
                </div>
                <div class="ml-md-2">
                    <button class="btn btn-link">Группировать по коду</button>
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
                    if(! this.clients.find(x => x.id === item.info.owner.id)){
                        this.clients.push(item.info.owner);
                    }
                },this);
            },
            sortByClient() {

            }
        },
        watch:{
            selectedClient(){
                if(this.selectedClient)
                    this.items = this.storedItems.filter((item) => {
                        return item.info.owner.id === this.selectedClient.id
                    },this);
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
                selectedClient:null,
                clients:[],
                fields: {
                    'info.item.name': {
                        label: 'Наименование',
                        sortable: true
                    },
                    code: {
                        label: 'Код',
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
                    'storage_history.storage.name': {
                        label: 'Склад'
                    }
                }
            }
        },
        components: {
            'StoredTable': require('./StoredTable.vue').default
        },
    }
</script>
