<template>
    <div class="container">
        <table-card
            :fields="fields"
            :isBusy="isBusy"
            :items="items"
            :customCells="customCells"
            class="shadow"
            primary-key="id"
            responsive
            striped>
            <template #header>
                <div class="card-header">
                    <div class="row align-items-baseline">
                        <div class="col-md-4">Список наименований</div>
                        <div class="col-md-8 text-right"><a class="btn btn-primary" href="/items/create">Добавить</a></div>
                    </div>
                </div>
            </template>

            <template slot="onlyCustomPrice" slot-scope="{item}">
                <b-check :checked="item.onlyCustomPrice" disabled></b-check>
            </template>

            <template slot="applyDiscount" slot-scope="{item}">
               <b-check :checked="item.applyDiscount" disabled></b-check>
            </template>

            <template #footer>
                <div class="card-footer">
                    <main-paginator :flowable="flowable" :onPageChange="getItems"
                                    :pagination="pagination"></main-paginator>
                </div>
            </template>
        </table-card>
    </div>
</template>

<script>
    export default {
        name: "ItemsTable",
        mounted() {
            this.getItems();
        },
        props: {
            flowable: {
                type: Boolean,
                default: true
            },
        },
        data() {
            return {
                pagination: {
                    last_page: null,
                    current_page: null
                },
                items: [],
                isBusy: false,
                customCells:['applyDiscount', 'onlyCustomPrice'],
                fields: {
                    name: {
                        label: 'Наименование',
                        sortable: true
                    },
                    unit: {
                        label: 'Единица',
                        sortable: false
                    },
                    'tariff.name': {
                        label: 'Тариф',
                        sortable: true
                    },
                    onlyCustomPrice: {
                        label: 'Всегда дог-ная цена',
                        sortable: true
                    },
                    applyDiscount: {
                        label: 'Учитывать скидку',
                        sortable: true
                    }
                }
            }
        },
        methods: {
            getItems(page = 1) {
                if (this.trips)
                    return;

                this.isBusy = true;

                let action = '/items/all?paginate=10&page=' + page;

                axios.get(action)
                    .then(response => {
                        this.pagination = response.data;
                        if (this.flowable)
                            response.data.data.forEach(item => {
                                this.items.push(item);
                            });
                        else
                            this.items = response.data.data;
                        this.$nextTick(() => {
                            this.isBusy = false;
                        })
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
            'TableCard': require('../common/TableCard.vue').default
        }
    }
</script>
