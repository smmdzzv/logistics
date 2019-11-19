<template>
    <table-card
        :borderless="borderless"
        :fields="fields"
        :fixed="fixed"
        :hover="hover"
        :items="items"
        :responsive="responsive"
        :select-mode="selectMode"
        :selectable="selectable"
        :sticky-header="tableHeight"
        :striped="striped"
        :tableBusy="false"
        :customCells = cells
        excelFileName="Список таможенных кодов"
        excelSheetName="Лист 1"
        primaryKey="id"
        responsive>

        <template slot="calculateByPiece" slot-scope="{item}">
            <span v-if="item.calculateByPiece">
                ✓
            </span>
            <span v-else>

            </span>
        </template>

        <template #header>
            <div class="row align-items-baseline">
                <div class="col-12 col-md-6 mb-3 mb-md-0">
                    <div class=" mr-auto">Коды</div>
                </div>
            </div>
        </template>

    </table-card>
</template>

<script>
    import TableCardProps from '../common/TableCardProps.vue'

    export default {
        name: "CustomsCodesTable",
        mixins: [TableCardProps],
        props:{
            customCodes:{
                type:Array,
                requested: true
            }
        },
        mounted(){
            this.items = this.customCodes;
        },
        data(){
            return{
                cells:['calculateByPiece'],
                items: [],
                fields:{
                    shortName:{
                        label:'Наз-ие',
                        sortable:true
                    },
                    code:{
                        label:'Код',
                        sortable:true
                    },
                    price:{
                        label:'Цена',
                        sortable:true
                    },
                    rate:{
                        label:'Ставка',
                        sortable:true
                    },
                    percentage:{
                        label:'%',
                        sortable:true
                    },
                    vat:{
                        label:'НДС',
                        sortable:true
                    },
                    calculateByPiece:{
                        label:'Расчет по шт.',
                        sortable:true
                    }
                }
            }
        },
        components: {
            'TableCard': require('../common/TableCard.vue').default
        }

    }
</script>
