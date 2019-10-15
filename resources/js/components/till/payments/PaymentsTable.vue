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
                <div class="row align-items-baseline">
                    <div class="pl-2 mr-auto">История платежей</div>
                    <div class="pr-2 ml-auto" v-if="branches">
                        <b-select v-model="selectedBranch">
                            <option :value="null">Все филиалы</option>
                            <option v-for="branch in branches" :value="branch" :key="branch.id">{{branch.name}}</option>
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
            branches:{
                type:Array,
                required:false
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
                selectedBranch:null,
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
                let action = '/payments/' + this.type + '/all';
                if(this.selectedBranch)
                    action = '/payments/in/' + this.selectedBranch.id;
                return action;
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
        watch:{
            selectedBranch(){
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
