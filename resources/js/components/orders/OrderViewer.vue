<template>
    <div>
        <div class="row">
            <div class="col-12 col-sm-6">
                <p> Клиент: <span class="font-weight-bold">{{order.owner.name}}</span></p>
            </div>
            <div class="col-12 col-sm-6 text-left text-sm-right">
                <p class="badge badge-primary p-2">
                    Статус: <span class="text">{{getStatus()}}</span>
                </p>
            </div>
        </div>

        <b-table :fields="fields"
                 :items="order.stored_item_infos"
                 foot-clone
                 hover
                 no-footer-sorting
                 outlined
                 primary-key="id"
                 responsive
                 striped>
            <template slot="count" slot-scope="{item}">
                <span>{{item.count}} ({{item.item.unit}})</span>
            </template>

            <template slot="FOOT[item.name]" slot-scope="data">
                <span>Итого</span>
            </template>
            <template slot="FOOT[count]" slot-scope="data">
                <span> </span>
            </template>
            <template slot="FOOT[billing_info.totalWeight]" slot-scope="data">
                <span>{{order.totalWeight}}</span>
            </template>
            <template slot="HEAD[billing_info.totalCubage]" slot-scope="data">
                <span>Общая кубатура, м<sup>3</sup></span>
            </template>
            <template slot="FOOT[billing_info.totalCubage]" slot-scope="data">
                <span>{{order.totalCubage}}</span>
            </template>
            <template slot="FOOT[billing_info.totalDiscount]" slot-scope="data">
                <span>{{order.totalDiscount}}</span>
            </template>
            <template slot="FOOT[billing_info.totalPrice]" slot-scope="data">
                <span>{{order.totalPrice}}</span>
            </template>

            <template slot="HEAD[id]">
                <img @click="showShortInfo()" class="icon-btn-sm" src="/svg/barcode.svg">
            </template>
            <template slot="id" slot-scope="data">
                <img @click="showShortInfo(data)" class="icon-btn-sm" src="/svg/barcode.svg">
            </template>
            <template slot="FOOT[id]">
                <span></span>
            </template>
        </b-table>

        <!--        <stored-item-short-info v-for="item in itemsToShow" :storedItem="item" :key="item.id"></stored-item-short-info>-->

        <b-modal @hidden="onModalHidden()"
                 cancel-title="Отменить"
                 id="shortItemInfoModal"
                 no-close-on-esc
                 ok-title="Да"
                 title="Распечатать бирки?">
            <template v-for="item in itemsToShow">
                <stored-item-short-info :key="stored.id"
                                        :storedItemInfo="item"
                                        :storedItem="stored"
                                        v-for="stored in item.stored_items"/>
            </template>
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
                        label: 'Наименование',
                        sortable: true
                    },
                    count: {
                        label: 'Кол-во',
                        sortable: true
                    },
                    'billing_info.totalWeight': {
                        label: 'Общий вес, кг',
                        sortable: true
                    },
                    'billing_info.totalCubage': {
                        label: 'Общая кубатура, м3',
                        sortable: true
                    },
                    'billing_info.totalDiscount': {
                        label: 'Скидка',
                        sortable: true
                    },
                    'billing_info.totalPrice': {
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
                console.log(data);
                if (data)
                    this.itemsToShow.push(data.item);
                else
                    this.itemsToShow = this.order.stored_item_infos;
                this.$bvModal.show('shortItemInfoModal');
            },
            onModalHidden(e) {
                this.itemsToShow = [];
            }
        },
        components: {
            'StoredItemShortInfo': require('../stored/StoredItemShortInfo').default
        }
    }
</script>

<style scoped>

</style>
