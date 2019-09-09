<template>
    <div class="container col-12">
        <div class="card">
            <div class="card-header">
                История тарифных планов
            </div>
            <div class="">
                <b-table :items="histories"
                         :fields="fields"
                         primary-key="id"
                         striped
                         outlined
                         hover
                         responsive>
                </b-table>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        name: "TariffHistoriesViewer",
        props:{
            histories: {
                type: Array,
                required: true
            }
        },
        data(){
            return{
                fields:{
                    'tariff.name':{
                        label: 'Тариф',
                        sortable: true
                    },
                    lowerLimit:{
                        label: 'Нижний предел',
                        sortable: true
                    },
                    mediumLimit:{
                        label: 'Средний предел',
                        sortable: true
                    },
                    upperLimit:{
                        label: 'Верхний предел',
                        sortable: true
                    },
                    pricePerCube:{
                        label: 'Цена за куб',
                        sortable: true
                    },
                    discountForLowerLimit:{
                        label: 'Скидка НП',
                        sortable: true
                    },
                    discountForMediumLimit:{
                        label: 'Скидка СП',
                        sortable: true
                    },
                    agreedPricePerKg:{
                        label:'Договорная',
                        sortable: true
                    },
                    maxWeightPerCube:{
                        label: 'Макс. вес на куб',
                        sortable: true
                    },
                    pricePerExtraKg:{
                        label: 'Цена за доп. кг',
                        sortable: true
                    },
                    maxWeight:{
                        label: 'Общий вес',
                        sortable: true
                    },
                    maxCubage:{
                        label: 'Общая кубатура',
                        sortable: true
                    }
                }
            }
        }
    }
</script>

<style scoped>

</style>
