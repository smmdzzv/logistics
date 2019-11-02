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
        :tableBusy="isBusy"
        class="shadow"
        excelFileName="История платежей"
        excelSheetName="Все платежи"
        primaryKey="id"
        responsive>
        <template #header>
            <div class="form-row align-items-baseline">
                <!--                <div class="col-12 col-md-2 mb-3 mb-md-0">-->
                <!--                    <div class=" mr-auto">История платежей</div>-->
                <!--                </div>-->

                <div class="form-row form-group col-12 ">
                    <div class="col-12 mb-3 mb-md-0 col-md-2">
                        <label>Плательщик</label>
                        <search-user-dropdown :selected="userSelected"
                                              id="user"
                                              placeholder="Введите ФИО или код пользователя"
                                              url="/user/find?userInfo="/>
                    </div>

                    <div class="col-12 col-md-2 mb-3 mb-md-0 col-md-2" v-if="branches">
                        <label>Тип</label>
                        <b-select v-model="selectedType">
                            <option :value="null">Все типы</option>
                            <option value="in">Доход</option>
                            <option value="out">Расход</option>
                        </b-select>
                    </div>

                    <div class="col-12 col-md-2 mb-3 mb-md-0 col-md-2" v-if="branches">
                        <label>Филиал</label>
                        <b-select v-model="selectedBranch">
                            <option :value="null">Все филиалы</option>
                            <option :key="branch.id" :value="branch" v-for="branch in branches">{{branch.name}}
                            </option>
                        </b-select>
                    </div>

                    <div class="col-12 col-md-2 mb-3 mb-md-0 col-md-2" v-if="branches">
                        <label>Валюта</label>
                        <b-select v-model="selectedCurrency">
                            <option :value="null">Все валюты</option>
                            <option :key="currency.id" :value="currency" v-for="currency in currencies">
                                {{currency.isoName}}
                            </option>
                        </b-select>
                    </div>

                    <div class="col-12 col-md-2 mb-3 mb-md-0 col-md-2">
                        <label>От</label>
                        <input class="form-control" type="date" v-model="dateFrom">
                    </div>

                    <div class="col-12 col-md-2 mb-3 mb-md-0 col-md-2">
                        <label>До</label>
                        <input class="form-control" type="date" v-model="dateTo">
                    </div>
                </div>
                <div class="form-row">

                </div>
            </div>
        </template>

        <template #footer>
            <div class="card-footer">
                <main-paginator :flowable="flowable" :onPageChange="getItems"
                                :pagination="pagination"></main-paginator>
            </div>
        </template>
    </table-card>
</template>

<script>
    import TableCardProps from '../../common/TableCardProps.vue'

    export default {
        name: "PaymentsTable",
        mixins: [TableCardProps],
        mounted() {
            if (this.payments)
                this.items = this.payments;
            this.getItems();
        },
        props: {
            comment: {
                type: String
            },
            branches: {
                type: Array,
                required: false
            },
            currencies: {
                type: Array,
                required: false
            },
            payments: {
                type: Array,
                required: false
            },
            flowable: {
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
                selectedUser: null,
                selectedCurrency: null,
                dateFrom: null,
                dateTo: null,
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
                    comment: {
                        label: "Комментарий"
                    },
                    'cashier.name': {
                        label: 'Кассир',
                        sortable: true
                    },
                },
            }
        },
        methods: {
            prepareUrl() {
                let action = '/payments/filtered?';

                if (this.selectedBranch)
                    action += `branch=${this.selectedBranch.id}&`;
                if (this.selectedType)
                    action += 'type=' + this.selectedType + '&';
                if(this.selectedUser)
                    action += 'user=' + this.selectedUser.id + '&';
                if(this.selectedCurrency)
                    action += 'currency=' + this.selectedCurrency.id + '&';
                if(this.dateFrom)
                    action += 'from=' + this.dateFrom + '&';
                if(this.dateTo)
                    action += 'to=' + this.dateTo + '&';
                return action;
            },
            userSelected(user) {
                this.selectedUser = user;
                this.getItems();
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
            },
        },
        watch: {
            selectedBranch() {
                this.getItems();
            },
            selectedType() {
                this.getItems();
            },
            selectedUser() {
                this.getItems();
            },
            selectedCurrency() {
                this.getItems();
            },
            dateFrom() {
                this.getItems();
            },
            dateTo() {
                this.getItems();
            }
        },
        components: {
            'MainPaginator':
            require('../../common/MainPaginator.vue').default,
            'TableCard':
            require('../../common/TableCard.vue').default,
            'SearchUserDropdown':
            require('../../users/SearchUserDropdown.vue').default,
        }
    }
</script>

<style scoped>

</style>
