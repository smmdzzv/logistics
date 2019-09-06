<template>
    <div>
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
            <template slot="FOOT[billing_info.totalCubage]" slot-scope="data">
                <span>{{order.totalCubage}}</span>
            </template>
            <template slot="FOOT[billing_info.totalDiscount]" slot-scope="data">
                <span>{{order.totalDiscount}}</span>
            </template>
            <template slot="FOOT[billing_info.totalPrice]" slot-scope="data">
                <span>{{order.totalPrice}}</span>
            </template>
        </b-table>
    </div>
</template>

<script>
    export default {
        name: "OrderViewer",
        props:{
            order: Object
        },
        data() {
            return{
                fields: {
                    'item.name':{
                        label: 'Наименование',
                        sortable: true
                    },
                    count:{
                        label: 'Кол-во',
                        sortable: true
                    },
                    'billing_info.totalWeight':{
                        label: 'Общий вес, кг',
                        sortable: true
                    },
                    'billing_info.totalCubage':{
                        label: 'Общая кубатура, м3',
                        sortable: true
                    },
                    'billing_info.totalDiscount':{
                        label: 'Скидка',
                        sortable: true
                    },
                    'billing_info.totalPrice':{
                        label: 'Сумма, $',
                        sortable: true
                    }
                }
            }
        }
    }
</script>

<style scoped>

</style>
