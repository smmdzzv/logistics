<template>
    <div class="container-fluid">
        <div class="col-12">
            <table-card
                primary-key="id"
                :items="items"
                :fields="fields"
            >
                <template v-slot:header>
                    Таможенная декларация
                </template>

                <template v-slot:cell(index)="data">
                    {{data.index + 1}}
                </template>

                <template v-slot:cell(code)="{item}">
                    <b-select style="width: 200px" v-model="item.customsCodeId" @change="onItemCustomsCodeChange(item)">
                        <b-select-option :value="null" disabled>-- Таможенный код не найден --</b-select-option>
                        <b-select-option v-for="code in customsCodes" :value="code.id" :key="code.id">
                            {{code.code}} {{code.name}}
                        </b-select-option>
                    </b-select>
                </template>
                <template v-slot:cell(count)="{item}">
                    <input type="number" class="form-control" step="1" v-model="item.count"
                           @change="onWeightCountChange(item)">
                </template>
                <template v-slot:cell(totalWeight)="{item}">
                    <input type="number" class="form-control" step="0.001" v-model="item.totalWeight"
                           @change="onWeightCountChange(item)">
                </template>

                <template v-slot:cell(info.customsCodeTax.isCalculatedByPiece)="{item}">
                    <b-check  disabled :checked="item.info.customsCodeTax.isCalculatedByPiece"></b-check>
                </template>
            </table-card>
        </div>
    </div>
</template>

<script>
    import TableCard from "../common/TableCard";
    import {groupBy} from "lodash/collection";

    export default {
        name: "CustomsReportEditor",
        components: {TableCard},
        props: {
            storedItems: Array,
            customsCodes: Array
        },
        mounted() {
            this.mapStoredItems()
        },
        data() {
            return {
                items: [],
                fields: [
                    {
                        key: 'index',
                        label: '№'
                    },
                    {
                        key: 'code',
                        label: 'Код'
                    },
                    {
                        key: 'count',
                        label: 'Кол-во'
                    },
                    {
                        key: 'totalWeight',
                        label: 'Общий Вес (кг)'
                    },
                    {
                        key: 'info.customsCodeTax.price',
                        label: 'Цена за тонну/шт'
                    },
                    {
                        key: 'info.customsCodeTax.isCalculatedByPiece',
                        label: 'Поштучно'
                    },
                    {
                        key: 'info.customsCodeTax.totalRate',
                        label: 'Ставка %'
                    },
                    {
                        key: 'totalDuty',
                        label: 'Пошлина'
                    }
                ]
            }
        },
        methods: {
            mapStoredItems() {
                let groupedByCode = groupBy(this.storedItems, i => i.info.customsCodeTax.id);
                // var vue = this;
                this.items = Object.keys(groupedByCode).map((key, index) =>
                    groupedByCode[key].reduce((accumulator, stored) => {
                        accumulator.totalWeight += stored.info.weight;
                        accumulator.totalPrice += stored.info.billingInfo.pricePerItem;
                        accumulator.count++;
                        accumulator.info = stored.info;
                        return accumulator;
                    }, {
                        totalWeight: 0,
                        totalPrice: 0,
                        count: 0
                    }),
                ).map(s => {
                    s.customsCodeId = s.info.customs_code_id;
                    s.totalDuty = this.calculateDuty(s.info.customsCodeTax, s.totalWeight, s.count);
                    return s;
                });
            },
            calculateDuty(customsCodeTax, weight, quantity) {
                let duty = 0;
                if (customsCodeTax.isCalculatedByPiece) {
                    duty = quantity * customsCodeTax.price * customsCodeTax.totalRate / 100;
                } else {
                    let tonnage = weight / 1000;
                    duty = tonnage * customsCodeTax.price * customsCodeTax.totalRate / 100
                }
                return Math.round(duty * 100) / 100;
            },
            onItemCustomsCodeChange(item) {
                item.info.customsCode = this.customsCodes.find(c => c.id === item.customsCodeId);
                item.info.customsCodeTax = item.info.customsCode.tax;
                item.totalDuty = this.calculateDuty(item.info.customsCodeTax, item.totalWeight, item.length)
            },
            onWeightCountChange(item) {
                item.totalDuty = this.calculateDuty(item.info.customsCodeTax, item.totalWeight, item.length)
            }
        },
    }
</script>
