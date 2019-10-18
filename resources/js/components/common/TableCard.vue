<template>
    <div class="card">
        <div class="card-header">
            <div class="row align-items-baseline">
                <div class="col-10 col-md-11">
                    <slot name="header">

                    </slot>
                </div>

                <div class="ml-auto" v-if="excelColumns">
                    <vue-excel-xlsx
                        :columns="excelColumns"
                        :data="excelData"
                        :filename="excelFileName"
                        :sheetname="excelSheetName"
                        class="btn">
                        <img class="icon-btn-md" src="/svg/excel.svg">
                    </vue-excel-xlsx>
                </div>
            </div>
        </div>


        <b-table :borderless="borderless"
                 :busy="isBusy"
                 :fields="fields"
                 :fixed="fixed"
                 :items="items"
                 :primary-key="primaryKey"
                 :responsive="responsive"
                 :select-mode="selectMode"
                 :selectable="selectable"
                 :sticky-header="tableHeight"
                 :striped="striped"
                 :tbody-tr-class="rowClass"
                 @row-clicked="onRowClick">

            <template v-slot:table-busy>
                <div class="text-center text-info my-2">
                    <b-spinner class="align-middle"></b-spinner>
                </div>
            </template>

            <template slot="selected" slot-scope="data">
                <span :class="checkedClass" v-if="isSelected(data.item)">
                    <slot name="selectedCell">&check;</slot>
                </span>
                <span v-else>
                    <slot name="notSelectedCell"></slot>
                </span>
            </template>

            <template :slot="cell" slot-scope="data" v-for="cell in customCells">
                <slot :name="cell" v-bind:item="data.item"></slot>
            </template>
        </b-table>

        <slot name="footer">

        </slot>
    </div>
</template>

<script>
    import ExcelDataPreparatory from './ExcelDataPreparatory.vue'

    export default {
        name: "TableCard",
        mixins: [ExcelDataPreparatory],
        props: {
            tableHeight: {
                type: String,
                default: '500px'
            },
            items: {
                type: Array,
                required: true
            },
            fields: {
                type: Object,
                required: true
            },
            primaryKey: {
                type: String,
                required: true
            },
            selectable: {
                type: Boolean,
                default: false
            },
            selectMode: {
                type: String,
                default: 'single'
            },
            striped: {
                type: Boolean,
                default: false
            },
            borderless: {
                type: Boolean,
                default: false
            },
            responsive: {
                type: Boolean,
                default: false
            },
            fixed: {
                type: Boolean,
                default: false
            },
            selectedRowClass: {
                type: String,
                default: 'table-success'
            },
            checkedClass: {
                type: String,
                default: 'text-success'
            },
            isBusy: {
                type: Boolean,
                default: false
            },
            customCells: {
                type: Array,
                default: () => []
            },
            excelSheetName: {
                type: String,
                default: 'Лист 1'
            },
            excelFileName: {
                type: String,
                default: 'Ajoibot Logistics Export'
            }
        },
        methods: {
            onRowClick(item) {
                if (!this.selectable)
                    return;
                if (this.isSelected(item)) {
                    this.selected = this.selected.filter(function (stored) {
                        return stored.id !== item.id
                    })
                } else {
                    this.selected.push(item)
                }

                return this.$emit('itemsSelected', this.selected);
            },
            isSelected(item) {
                return this.selected.find(function (selected) {
                    return selected.id === item.id;
                });
            },
            rowClass(item, type) {
                if (!item) return;
                if (this.isSelected(item)) return this.selectedRowClass
            }
        },
        data() {
            return {
                selected: []
            }
        },
    }
</script>
