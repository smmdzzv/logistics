<template>
    <div>
        <div class="row align-items-baseline">
            <div class="col-md-5">
                <p> Клиент: <span class="font-weight-bold">{{order.owner.code}}</span></p>
            </div>
            <div class="col-md-7 row   align-items-baseline">
                <button class="ml-md-auto btn btn-link" @click="updateOrderPrice">
                    Обновить стоимость
                </button>

<!--                <p class="ml-3 badge badge-primary p-2" v-if="order.status !== 'completed'">-->
<!--                    Статус: <span class="text">{{getStatus()}}</span>-->
<!--                </p>-->
<!--                <p class="ml-3 badge badge-secondary p-2" v-else>-->
<!--                    Статус: <span class="text">{{getStatus()}}</span>-->
<!--                </p>-->
            </div>
        </div>

        <b-table :fields="fields"
                 :items="order.storedItemInfos"
                 foot-clone
                 hover
                 no-footer-sorting
                 outlined
                 primary-key="id"
                 responsive
                 striped>
            <template v-slot:head(customsCode.code)>
                <span id="code">Код</span>
                <b-tooltip target="code" trigger="hover">
                    Таможенный код
                </b-tooltip>
            </template>

            <template v-slot:head(count)>
                <span id="count-header">Кол-во мест</span>
                <b-tooltip target="count-header" trigger="hover">
                    Количество мест товара. Общее кол-во/Остаток
                </b-tooltip>
            </template>

            <template v-slot:head(buttons)>
                <img @click="showShortInfo()" class="icon-btn-sm" src="/svg/barcode.svg">
            </template>

            <template v-slot:cell(count)="{item}">
                <span>{{item.count}}/{{item.storedItems.length}} {{item.item.unit}}</span>
            </template>

            <template v-slot:cell(buttons)="data">
                <img @click="showShortInfo(data)" class="icon-btn-sm" src="/svg/barcode.svg">
            </template>

            <template v-slot:foot(item.name)>
                <span>Итого</span>
            </template>

            <template v-slot:foot(customsCode.code)>
                <span></span>
            </template>

            <template v-slot:foot(tariff.name)>
                <span></span>
            </template>

            <template v-slot:foot(count)>
                <span> </span>
            </template>

            <template v-slot:cell(billingInfo.totalWeight)="{item}">
                <span>{{item.billingInfo.totalWeight.toFixed(3)}}</span>
            </template>


            <template v-slot:cell(billingInfo.totalCubage)="{item}">
                <span>{{item.billingInfo.totalCubage.toFixed(3)}}</span>
            </template>

            <template v-slot:cell(billingInfo.totalDiscount)="{item}">
                <span>{{item.billingInfo.totalDiscount.toFixed(2)}}</span>
            </template>

            <template v-slot:cell(billingInfo.totalPrice)="{item}">
                <span>{{item.billingInfo.totalPrice.toFixed(2)}}</span>
            </template>

            <template v-slot:foot(billingInfo.totalWeight)>
                <span>{{order.totalWeight.toFixed(3)}}</span>
            </template>

            <template v-slot:foot(billingInfo.totalCubage)>
                <span>Объем, м<sup>3</sup></span>
            </template>

            <template v-slot:foot(billingInfo.totalCubage)>
                <span>{{order.totalCubage.toFixed(3)}}</span>
            </template>

            <template v-slot:foot(billingInfo.totalDiscount)>
                <span>{{order.totalDiscount.toFixed(2)}}</span>
            </template>

            <template v-slot:foot(billingInfo.totalPrice)>
                <span>{{order.totalPrice.toFixed(2)}}</span>
            </template>
        </b-table>

        <b-modal @hidden="onModalHidden()"
                 hide-footer
                 id="shortItemInfoModal">
            <template v-slot:modal-header>
                <button @click="printLabels" class="btn btn-primary">Печать</button>
            </template>
            <vue-easy-print :tableShow="true" ref="easyPrint">
                <template v-for="item in itemsToShow">
                    <stored-item-short-info class="bar-card"
                                            :key="stored.id"
                                            :storedItem="stored"
                                            :storedItemInfo="item"
                                            v-for="stored in item.storedItems"/>
                </template>
            </vue-easy-print>
        </b-modal>
    </div>
</template>

<script>
import {hideBusySpinner, showBusySpinner} from "../../tools";

    export default {
        name: "OrderViewer",
        props: {
            order: Object
        },
        computed: {
            orderUrl: function () {
                return this.getBaseUrl() + '/order/' + this.order.id;
            }
        },
        data() {
            return {
                fields: [
                    {
                        key: 'item.name',
                        label: 'Тип',
                        sortable: true
                    },
                    {
                        key: 'customsCode.code',
                        label: 'Код',
                        sortable: true
                    },
                    {
                        key: 'tariff.name',
                        label: 'Тариф',
                        sortable: true
                    },
                    {
                        key: 'count',
                        label: 'Кол-во мест',
                        sortable: true
                    },
                    {
                        key: 'billingInfo.totalWeight',
                        label: 'Вес, кг',
                        sortable: true
                    },
                    {
                        key: 'billingInfo.totalCubage',
                        label: 'Объем, м3',
                        sortable: true
                    },
                    {
                        key: 'billingInfo.totalDiscount',
                        label: 'Скидка',
                        sortable: true
                    },
                    {
                        key: 'billingInfo.pricePerItem',
                        label: 'Цена за ед, $',
                        sortable: true
                    },
                    {
                        key: 'billingInfo.totalPrice',
                        label: 'Сумма, $',
                        sortable: true
                    },
                    {
                        key: 'buttons',
                        label: ''
                    }
                ],
                itemsToShow: [],
                printUrl: '/print/order-labels/' + this.order.id
            }
        },
        methods: {
            getBaseUrl() {
                let url = window.location;
                return url.protocol + "//" + url.host
            },
            getStatus() {
                switch (this.order.status) {
                    case "accepted":
                        return "Принят к обработке";
                    case "delivering":
                        return "В пути";
                    case "delivered":
                        return "Доставлен";
                    case "completed":
                        return "Выполнен"
                }
            },
            showShortInfo(data) {
                if (data){
                    this.itemsToShow.push(data.item);
                    this.printUrl = '/print/info-labels/' + data.item.id;
                }
                else{
                    this.itemsToShow = this.order.storedItemInfos;
                    this.printUrl = '/print/order-labels/' + this.order.id;
                }

                this.itemsToShow = this.itemsToShow.filter(function (item) {
                    return item.storedItems.length > 0;
                });

                if (this.itemsToShow.length > 0)
                    this.$bvModal.show('shortItemInfoModal');
            },
            onModalHidden(e) {
                this.itemsToShow = [];
            },
            printLabels() {
                // this.$refs.easyPrint.print();
                // window.location.href = this.printUrl;
                window.open(this.printUrl, "_blank");
            },
            // pricePerCountPlace(data) {
            //     if (data && data.item.placeCount) {
            //
            //         let price = data.item.billingInfo.totalPrice / (data.item.count * data.item.placeCount);
            //         return price.toFixed(2);
            //     }
            // },
            async updateOrderPrice() {
                showBusySpinner();
                try {
                    const response = await axios.post(`/order/${this.order.id}/update-price`);
                    window.location.reload();
                } catch (e) {

                }finally {
                    hideBusySpinner();
                }
            }
        },
        components: {
            'StoredItemShortInfo': require('../stored/StoredItemShortInfo').default
        }
    }
</script>

<style scoped>
    /*.bar-card {*/
    /*    width: 6cm;*/
    /*    height: 4cm;*/
    /*    !*overflow: hidden;*!*/
    /*}*/

    /*@page {*/
    /*    size: 6cm 4cm;*/
    /*    margin: 0;*/
    /*}*/

    /*@media print {*/
    /*    body {*/
    /*        margin: 0;*/
    /*        padding: 0;*/
    /*        font-weight: bold;*/
    /*    }*/
    /*    .bar-card {*/
    /*        margin: 0;*/
    /*        border: initial;*/
    /*        border-radius: initial;*/
    /*        width: 6cm;*/
    /*        height: 4cm;*/
    /*        box-shadow: initial;*/
    /*        background: initial;*/
    /*        page-break-after: always;*/
    /*    }*/
    /*}*/

    .bar-card {
        font-family: Arial;
        font-size: 12px;
    }

    /*@page {*/
    /*    size: USER;*/
    /*    margin: 0;*/
    /*}*/

    /*@media print {*/
    /*    body {*/
    /*        margin: 0;*/
    /*        padding: 0;*/
    /*        font-weight: bold;*/
    /*        font-family: Arial !important;*/
    /*        font-size: 12px;*/
    /*    }*/
    /*    .bar-card {*/
    /*        margin: 0;*/
    /*        padding: 15px 0 0 15px;*/
    /*        border: initial;*/
    /*        border-radius: initial;*/
    /*        width: initial;*/
    /*        min-height: initial;*/
    /*        box-shadow: initial;*/
    /*        background: initial;*/
    /*        page-break-after: always;*/
    /*        font-family: Arial !important;*/
    /*        font-size: 12px;*/
    /*    }*/
    /*}*/
</style>
