<template>
    <table-card
        :customCells="customCells"
        :fields="fields"
        :isBusy="isBusy"
        :items="items"
        :striped="striped"
        class="shadow"
        primary-key="id"
        responsive>
        <template #header>
            <div class="card-header">
                История платежей
            </div>
        </template>

        <!--            <template slot="view" slot-scope="{item}">-->
        <!--                <a :href="getDetailsUrl(item)" class="btn btn-outline-primary">Детали</a>-->
        <!--            </template>-->

        <template #footer>
            <div class="card-footer">
                <main-paginator :flowable="flowable" :onPageChange="getItems"
                                :pagination="pagination"></main-paginator>
            </div>
        </template>
    </table-card>
</template>

<script>
    export default {
        name: "PaymentsTable",
        mounted() {
            if (this.payments)
                this.items = this.payments;
            this.getItems();
        },
        props: {
            selectable: {
                type: Boolean,
                default: false
            },
            payments: {
                type: Array,
                required: false
            },
            flowable: {
                type: Boolean,
                default: false
            },
            striped: {
                type: Boolean,
                default: false
            },
            type: {
                type: String,
                default: 'in'
            }
        },
        data() {
            return {
                pagination: {
                    last_page: null,
                    current_page: null
                },
                items: [],
                isBusy: false,
                customCells: [],
                fields: {
                    created_at: {
                        label: 'Дата',
                        sortable: true
                    },
                    'account_to.description': {
                        label: 'Cчет зачисления',
                        sortable: true
                    },
                    'payer.name': {
                        label: 'Плательщик',
                        sortable: true
                    },
                    amount: {
                        label: 'Сумма',
                        sortable: true
                    },
                    'currency.isoName':{
                        label: 'Валюта',
                        sortable: true
                    },
                    'payment_item.title':{
                        label: 'Статья',
                        sortable: true
                    }
                }
            }
        },
        methods: {
            prepareUrl() {
                let action = '/payments/' + this.type;
                return action += '/all';
            },
            getItems(page = 1) {
                if (this.trips)
                    return;

                this.isBusy = true;

                let action = this.prepareUrl() + '?paginate=10&page=' + page;

                axios.get(action)
                    .then(response => {
                        this.pagination = response.data;
                        if (this.flowablePagination)
                            response.data.data.forEach(item => {
                                this.items.push(item);
                            });
                        else
                            this.items = response.data.data;
                    })
                    .catch(error => {
                        this.$root.showErrorMsg(
                            "Ошибка загрузки",
                            'Не удалось загрузить платежи. Обновите страницу'
                        )
                    })
                    .finally(
                        this.$nextTick(() => {
                            this.isBusy = false;
                        })
                    );
            }
        },
        components: {
            'MainPaginator': require('../../common/MainPaginator.vue').default,
            'TableCard': require('../../common/TableCard.vue').default
        }
    }
</script>

<style scoped>

</style>
