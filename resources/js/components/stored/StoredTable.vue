<template>
    <div class="card">
        <div class="card-header">
            <div class="row px-3 align-items-baseline">
                <slot name="header">
                    <div>
                        <span v-if="branches">Товары на складе</span>
                        <span v-else>Товары на всех складах</span>
                    </div>
<!--                    <div class="ml-0 ml-sm-auto">-->
<!--                        <template v-if="listGenerator">-->
<!--                            <button id="generate-btn" class="btn btn-link" @click="generateList">Сгенерировать список-->
<!--                            </button>-->
<!--                            <b-tooltip target="generate-btn" triggers="hover">-->
<!--                                Генерация списка происходит с учетом выбранного фильтра и товаров,-->
<!--                                добавленных на рейс вручную. Для сброса сгенерированного списка, обновите страницу-->
<!--                            </b-tooltip>-->
<!--                        </template>-->
<!--                    </div>-->
                    <template v-if="branches">
                        <div class="ml-3">
                            <select class="form-control custom-select" id="branch" v-model="selectedBranch">
                                <option :value="null">--Все склады--</option>
                                <option :key="branch.id" :value="branch" v-for="branch in branches">
                                    {{ branch.name }}
                                </option>
                            </select>
                        </div>
                    </template>
                </slot>
                <div class="ml-3" v-if="excelExport">
                    <vue-excel-xlsx
                        :columns="excelColumns"
                        :data="excelData"
                        :sheetname="selectedBranch? selectedBranch.name : 'Все склады'"
                        class="btn"
                        filename="Список товаров">
                        <img class="icon-btn-md" src="/svg/excel.svg">
                    </vue-excel-xlsx>
                </div>
            </div>
        </div>


        <b-table :busy="isBusy"
                 :fields="fields"
                 :hover="hover"
                 :items="items"
                 :selectable="selectable"
                 :striped="striped"
                 :tbody-tr-class="rowClass"
                 @row-clicked="itemSelected"
                 borderless
                 id="usersTable"
                 primary-key="id"
                 responsive
                 select-mode="single"
                 sticky-header="70vh">
            <template v-slot:table-busy>
                <div class="text-center text-info my-2">
                    <b-spinner class="align-middle"></b-spinner>
                </div>
            </template>

            <template v-slot:cell(owner)="{item}">
                <span>{{ item.info.owner.code }} {{ item.info.owner.name }}</span>
            </template>

            <template v-slot:cell(buttons)="{item}">
                <a :href="'/stored/' + item.id"><img class="icon-btn-sm" src="/svg/file.svg"></a>
            </template>

            <template v-slot:cell(selected)="{item}">
                <span class="text-success" v-if="isSelected(item)">&check;</span>
                <span v-else></span>
            </template>
        </b-table>

        <div class="card-footer">
            <main-paginator :flowable="flowable" :onPageChange="getItems"
                            :pagination="pagination"></main-paginator>
        </div>
    </div>
</template>

<script>
import ExcelDataPreparatory from '../common/ExcelDataPreparatory.vue'

export default {
    name: "StoredTable",
    mixins: [ExcelDataPreparatory],
    mounted() {
        this.setItems();
        this.getItems();
    },
    props: {
        branches: {
            type: Array,
            required: false,
            default: null
        },
        // listGenerator: {
        //     type: Boolean
        // },
        selectable: {
            type: Boolean,
            required: false,
            default: false
        },
        flowable: {
            type: Boolean,
            required: false,
            default: false
        },
        loadData: {
            type: Boolean,
            required: false,
            default: true
        },
        selectedItems: {
            type: Array,
            required: false,
            default: () => []
        },
        url: {
            type: String,
            required: false,
            default: '/stored/all'
        },
        providedItems: {
            type: Array,
            required: false,
        },
        striped: {
            type: Boolean,
            required: false,
            default: true
        },
        hover: {
            type: Boolean,
            required: false,
        },
        prepareUrl: {
            type: Function,
            default: (page, vm) => {
                let action = vm.url;
                if (vm.selectedBranch)
                    action = `/${vm.selectedBranch.id}/stored`;
                return action += '?paginate=7&page=' + page;
            }
        },
        tripId: {
            type: String
        },
        highlightRows: {
            type: Boolean,
            default: false
        },
        excelExport: {
            type: Boolean,
            default: false
        }
    },
    methods: {
        // Checks if item in all stored items array
        isInItems(item) {
            return this.items.find(function (stored) {
                return stored.id === item.id;
            });
        },
        // Checks if item in provided list stored items,
        // which are items belonging to certain trip
        isInProvidedItems(item) {
            return this.providedItems.find(function (stored) {
                return stored.id === item.id;
            });
        },
        getItems(page = 1) {
            if (!this.loadData)
                return;

            this.isBusy = true;

            let action = this.prepareUrl(page, this);

            axios.get(action)
                .then(response => {
                    this.pagination = response.data;
                    let items = response.data.data.filter(item => {
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
        itemSelected(item) {
            if (!this.selectable)
                return;
            if (this.isSelected(item)) {
                return this.$emit('onItemUnselected', item);
            } else {
                return this.$emit('onItemSelected', item);
            }
        },
        isSelected(item) {
            return this.selectedItems.find(function (selected) {
                return selected.id === item.id;
            });
        },
        rowClass(item, type) {
            if (!this.highlightRows || !item) return;
            if (this.isSelected(item))
                return 'table-success';
            else if (this.isInProvidedItems(item))
                return 'table-danger';
        },
        setItems() {
            if (this.providedItems)
                for (let item of this.providedItems) {
                    this.items.push(item);
                }

            if (this.selectedItems)
                for (let item of this.selectedItems) {
                    if (!this.isInItems(item))
                        this.items.push(item);
                }
        },
        // async generateList() {
        //     tShowSpinner();
        //     try {
        //         let action = `/trip/${this.tripId}/stored-items/generate`;
        //         if (this.selectedBranch)
        //             action += `?branch=${this.selectedBranch.id}`;
        //         const response = await axios.get(action);
        //         // this.items = response.data;
        //         for (let item of response.data) {
        //             if (!this.isInItems(item))
        //                 this.items.push(item);
        //
        //         }
        //
        //         this.$emit('onItemsSelected', response.data);
        //
        //     } catch (e) {
        //         this.$root.showErrorMsg(
        //             "Ошибка генерации",
        //             'Не удалось сгенерировать список. Попробуйте сгенерировать список позднее'
        //         )
        //     }
        //
        //     tHideSpinner();
        // }
    },
    computed: {
        currentPage() {
            return this.pagination.current_page;
        },
        lastPage() {
            return this.pagination.last_page
        }
    },
    watch: {
        selectedBranch: function () {
            this.items.splice(0, this.items.length);
            this.setItems();
            this.getItems();
        },
    },
    data() {
        return {
            selectedBranch: null,
            pagination: {
                last_page: null,
                current_page: null
            },
            items: [],
            isBusy: false,
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
                    key: 'info.weight',
                    label: 'Вес',
                    sortable: true
                },
                {
                    key: 'storageHistory.storage.name',
                    label: 'Склад'
                },
                {
                    key: 'buttons',
                    label: ''
                },
                {
                    key: 'selected',
                    label: ''
                }
            ]
        }
    }
}
</script>
