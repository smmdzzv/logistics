<template>
    <table-card
        :customCells="customCells"
        :fields="fields"
        :isBusy="isBusy"
        :items="items"
        :striped="striped"
        class="shadow"
        fixed
        primary-key="id"
        responsive>
        <template #header>
            <div class="card-header">
                <div class="row align-items-baseline">
                    <div class="pl-2 mb-1 col-12 col-sm-4 mr-auto">История платежей</div>
                    <div class="pl-2 pr-2 ml-md-auto" v-if="branches">
                        <b-select v-model="selectedType">
                            <option :value="null">Все типы</option>
                            <option value="in">Доход</option>
                            <option value="out">Расход</option>
                        </b-select>
                    </div>
                    <div class="pr-2" v-if="branches">
                        <b-select v-model="selectedBranch">
                            <option :value="null">Все филиалы</option>
                            <option :key="branch.id" :value="branch" v-for="branch in branches">{{branch.name}}</option>
                        </b-select>
                    </div>
                </div>

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
            branches: {
                type: Array,
                required: false
            },
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
                selectedBranch: null,
                selectedType: null,
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
                    'currency.isoName': {
                        label: 'Валюта',
                        sortable: true
                    },
                    'payment_item.title': {
                        label: 'Статья',
                        sortable: true
                    },

                    'cashier.name': {
                        label: 'Кассир',
                        sortable: true
                    },
                }
            }
        },
        methods: {
            prepareUrl() {
                let action = '/payments/filtered?';

                if (this.selectedBranch)
                    action += `branch=${this.selectedBranch.id}&`;
                if (this.selectedType)
                    action += 'type=' + this.selectedType + '&';

                return action;
            },
            getItems(page = 1) {
                if (this.trips)
                    return;

                this.isBusy = true;

                let action = this.prepareUrl() + 'paginate=15&page=' + page;

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
        watch: {
            selectedBranch() {
                this.getItems();
            },
            selectedType() {
                this.getItems();
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
