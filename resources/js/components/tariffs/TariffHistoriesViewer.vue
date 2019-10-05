<template>
    <div class="container col-12">
        <div class="card shadow">
            <div class="card-header">
                История тарифных планов
            </div>
            <div class="">
                <b-table :fields="fields"
                         :items="items"
                         :busy="isBusy"
                         hover
                         outlined
                         primary-key="id"
                         responsive
                         striped>

                    <template v-slot:table-busy>
                        <div class="text-center text-info my-2">
                            <b-spinner class="align-middle"></b-spinner>
                        </div>
                    </template>

                </b-table>
            </div>
            <div class="card-footer">
                <main-paginator :flowable="flowable" :onPageChange="getHistories" :pagination="pagination"></main-paginator>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        name: "TariffHistoriesViewer",
        mounted() {
            this.getHistories();
        },
        props: {
            flowable: {
                type: Boolean,
                required: false,
                default: false
            }
        },
        data() {
            return {
                pagination: {
                    last_page: null,
                    current_page: null
                },
                items:[],
                isBusy:false,
                fields: {
                    'tariff.name': {
                        label: 'Тариф',
                        sortable: true
                    },
                    lowerLimit: {
                        label: 'Нижний предел',
                        sortable: true
                    },
                    mediumLimit: {
                        label: 'Средний предел',
                        sortable: true
                    },
                    upperLimit: {
                        label: 'Верхний предел',
                        sortable: true
                    },
                    pricePerCube: {
                        label: 'Цена за куб',
                        sortable: true
                    },
                    discountForLowerLimit: {
                        label: 'Скидка НП',
                        sortable: true
                    },
                    discountForMediumLimit: {
                        label: 'Скидка СП',
                        sortable: true
                    },
                    agreedPricePerKg: {
                        label: 'Договорная',
                        sortable: true
                    },
                    maxWeightPerCube: {
                        label: 'Макс. вес на куб',
                        sortable: true
                    },
                    pricePerExtraKg: {
                        label: 'Цена за доп. кг',
                        sortable: true
                    },
                    maxWeight: {
                        label: 'Общий вес',
                        sortable: true
                    },
                    maxCubage: {
                        label: 'Общая кубатура',
                        sortable: true
                    }
                }
            }
        },
        methods:{
            async getHistories(page = 1){
                this.isBusy = true;

                let action = '/tariff-price-histories/all?paginate=10&page=' + page;

                try{
                    const response = await axios.get(action);
                    this.pagination = response.data;
                    if (this.flowablePagination)
                        response.data.data.forEach(item => {
                            this.items.push(item);
                        });
                    else
                        this.items = response.data.data;
                }
                catch (e) {
                    this.$root.showErrorMsg(
                        'Ошибка загрузки',
                        'Не удалось загрузить историю расценок. Попробуйте обновить список позднее'
                    )
                }

                this.$nextTick(() => {
                    this.isBusy = false;
                });
            }
        },
        computed: {
            currentPage() {
                return this.pagination.current_page;
            },
            lastPage() {
                return this.pagination.last_page
            }
        },
        components: {
            'MainPaginator': require('../common/MainPaginator.vue').default,
        }
    }
</script>

<style scoped>

</style>
