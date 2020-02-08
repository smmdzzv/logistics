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

                <p class="ml-3 badge badge-primary p-2" v-if="order.status !== 'completed'">
                    Статус: <span class="text">{{getStatus()}}</span>
                </p>
                <p class="ml-3 badge badge-secondary p-2" v-else>
                    Статус: <span class="text">{{getStatus()}}</span>
                </p>
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
            <template slot="count" slot-scope="{item}">
                <span>{{item.count}}/{{item.storedItems.length}} ({{item.item.unit}})</span>
            </template>

            <template slot="FOOT[item.name]">
                <span>Итого</span>
            </template>
            <template slot="FOOT[customsCodes.code]">
                <span></span>
            </template>
            <template slot="FOOT[count]">
                <span> </span>
            </template>
<!--            <template slot="FOOT[placeCount]">-->
<!--                <span> </span>-->
<!--            </template>-->
<!--            <template slot="FOOT[pricePerPlaceCount]">-->
<!--                <span> </span>-->
<!--            </template>-->
            <template slot="FOOT[billingInfo.totalWeight]">
                <span>{{order.totalWeight}}</span>
            </template>
            <template slot="HEAD[billingInfo.totalCubage]">
                <span>Объем, м<sup>3</sup></span>
            </template>
            <template slot="FOOT[billingInfo.totalCubage]">
                <span>{{order.totalCubage}}</span>
            </template>
            <template slot="FOOT[billingInfo.totalDiscount]">
                <span>{{order.totalDiscount}}</span>
            </template>
            <template slot="FOOT[billingInfo.totalPrice]">
                <span>{{order.totalPrice}}</span>
            </template>

            <template slot="HEAD[customsCodes.code]">
                <span id="code">Код</span>
                <b-tooltip target="code" trigger="hover">
                    Таможенный код
                </b-tooltip>
            </template>

            <template slot="HEAD[count]">
                <span id="count-header">Кол-во мест</span>
                <b-tooltip target="count-header" trigger="hover">
                    Количество мест товара. Общее кол-во/Остаток
                </b-tooltip>
            </template>

            <template slot="id" slot-scope="data">
                <img @click="showShortInfo(data)" class="icon-btn-sm" src="/svg/barcode.svg">
            </template>

            <template slot="HEAD[id]">
                <img @click="showShortInfo()" class="icon-btn-sm" src="/svg/barcode.svg">
            </template>

            <template slot="id" slot-scope="data">
                <img @click="showShortInfo(data)" class="icon-btn-sm" src="/svg/barcode.svg">
            </template>

<!--            <template slot="HEAD[pricePerPlaceCount]">-->
<!--                <span id="pricePerPlaceCountTitle">Цена</span>-->
<!--                <b-tooltip target="pricePerPlaceCountTitle" triggers="hover">-->
<!--                    Цена в расчете на единицу места-->
<!--                </b-tooltip>-->
<!--            </template>-->
<!--            <template slot="pricePerPlaceCount" slot-scope="data">-->
<!--                <span>{{pricePerCountPlace(data)}}</span>-->
<!--            </template>-->
        </b-table>

        <b-modal @hidden="onModalHidden()"
                 hide-footer
                 id="shortItemInfoModal">
            <template v-slot:modal-header>
                <button @click="printLabels" class="btn btn-primary">Печать</button>
            </template>
            <vue-easy-print :tableShow="true" ref="easyPrint">
                <template v-for="item in itemsToShow">
                    <stored-item-short-info :key="stored.id"
                                            :storedItem="stored"
                                            :storedItemInfo="item"
                                            v-for="stored in item.storedItems"/>
                </template>
            </vue-easy-print>
        </b-modal>
    </div>
</template>

<script>
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
                fields: {
                    'item.name': {
                        label: 'Тип',
                        sortable: true
                    },
                    'customsCodes.code': {
                        label: 'Код',
                        sortable: true
                    },
                    count: {
                        label: 'Кол-во мест',
                        sortable: true
                    },
                    // placeCount: {
                    //     label: 'Кол-во мест',
                    //     sortable: true
                    // },
                    // 'pricePerPlaceCount': {
                    //     label: 'Цена'
                    // },
                    'billingInfo.totalWeight': {
                        label: 'Вес, кг',
                        sortable: true
                    },
                    'billingInfo.totalCubage': {
                        label: 'Объем, м3',
                        sortable: true
                    },
                    'billingInfo.totalDiscount': {
                        label: 'Скидка',
                        sortable: true
                    },
                    'billingInfo.totalPrice': {
                        label: 'Сумма, $',
                        sortable: true
                    },
                    'id': {
                        label: ''
                    } //for barcode btn
                },
                itemsToShow: []
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
                if (data)
                    this.itemsToShow.push(data.item);
                else
                    this.itemsToShow = this.order.storedItemInfos;

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
                this.$refs.easyPrint.print();
            },
            // pricePerCountPlace(data) {
            //     if (data && data.item.placeCount) {
            //
            //         let price = data.item.billingInfo.totalPrice / (data.item.count * data.item.placeCount);
            //         return price.toFixed(2);
            //     }
            // },
            async updateOrderPrice() {
                tShowSpinner();
                try {
                    const response = await axios.post(`/order/${this.order.id}/update-price`);
                    window.location.reload();
                } catch (e) {
                    tHideSpinner();
                }
            }
        },
        components: {
            'StoredItemShortInfo': require('../stored/StoredItemShortInfo').default
        }
    }
</script>

<style scoped>

</style>
