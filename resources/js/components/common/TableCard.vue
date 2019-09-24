<template>
    <div class="card">
        <slot name="header">
            <div class="card-header">

            </div>
        </slot>

        <b-table :borderless="borderless"
                 :busy="isBusy"
                 :fields="fields"
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

            <template v-for="cell in customCells" :slot="cell" slot-scope="data">
                <slot :name="cell" v-bind:item="data.item"></slot>
            </template>
        </b-table>

        <slot name="footer">

        </slot>
    </div>
</template>

<script>
    export default {
        name: "TableCard",
        props: {
            tableHeight: {
                type: String,
                default: '400px'
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
            borderless:{
                type: Boolean,
                default: false
            },
            responsive: {
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
            customCells:{
                type: Array,
                default:()=>[]
            }
        },
        methods: {
            onRowClick(item) {
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
        }
    }
</script>
