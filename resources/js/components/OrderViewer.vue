<template>
    <div >
        <div class="row pl-1">
<!--            <barcode :value="orderUrl" :options='{displayValue:true, height:30, width:2}'></barcode>-->
        </div>
        <div class="row pl-1">
<!--            <qr-code value="Hello, World!" :options="{ width: 200 }"></qr-code>-->
        </div>
        <div class="row">
            <div class="col-md-8">
                <p>
                    Клиент: <span class="font-weight-bold">{{order.owner.name}}</span>
                </p>
            </div>
            <div class="col-md-4 text-right">
                <p class="badge badge-primary p-2">
                    Статус: <span class="text">{{getStatus()}}</span>
                </p>
            </div>
        </div>

        <b-table :items="order.stored_items"
                 :fields="fields"
                 primary-key="id"
                 striped
                 outlined
                 hover
                 responsive
                 foot-clone
                 no-footer-sorting>
            <template slot="FOOT[item.name]" slot-scope="data">
                <span>Итого</span>
            </template>
            <template slot="FOOT[count]" slot-scope="data">
                <span>{{Math.round(order.totalCount)}}</span>
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
               <img class="icon-btn-sm" src="/svg/barcode.svg" alt="delete-item" @click="showShortInfo()">
            </template>
            <template slot="id" slot-scope="data">
                <img class="icon-btn-sm" src="/svg/barcode.svg" alt="delete-item" @click="showShortInfo(data)">
            </template>
            <template slot="FOOT[id]">
                <span></span>
            </template>
        </b-table>

<!--        <stored-item-short-info v-for="item in itemsToShow" :storedItem="item" :key="item.id"></stored-item-short-info>-->

        <b-modal id="shortItemInfoModal"
                 size="sm"
                 no-close-on-esc
                 title="Распечатать бирки?"
                 ok-title="Да"
                 cancel-title="Отменить"
                 @hidden="onModalHidden()">
            <stored-item-short-info v-for="item in itemsToShow" :storedItem="item" :key="item.id"></stored-item-short-info>
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
                    'id':{
                        label:''
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
            getStatus(){
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
            showShortInfo(data){
                console.log(data);
                if(data)
                    this.itemsToShow.push(data.item);
                else
                    this.itemsToShow = this.order.stored_items;
                this.$bvModal.show('shortItemInfoModal');
            },
            onModalHidden(e){
                this.itemsToShow = [];
            }
        },
        components:{
            'StoredItemShortInfo': require('./items/StoredItemShortInfo').default
        }
    }
</script>

<style scoped>

</style>
