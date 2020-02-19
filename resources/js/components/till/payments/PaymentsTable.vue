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
        :isBusy="isBusy"
        :customCells="customCells"
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
                        <label>Плательщик (Клиент)</label>
                        <search-user-dropdown :selected="payerSelected"
                                              placeholder="Введите ФИО или код пользователя"
                                              url="/user/find?userInfo="/>
                    </div>

                    <div class="col-12 mb-3 mb-md-0 col-md-2">
                        <label>Получатель (Клиент)</label>
                        <search-user-dropdown :selected="payeeSelected"
                                              placeholder="Введите ФИО или код пользователя"
                                              url="/user/find?userInfo="/>
                    </div>

                    <div class="col-12 col-md-2 mb-3 mb-md-0 col-md-2">
                        <label>Статья</label>
                        <b-select id="item" v-model="selectedPaymentItem">
                            <option :value="null">Все статьи</option>
                            <option :key="item.id" :value="item" v-for="item in paymentItems">
                                {{item.title}}
                            </option>
                        </b-select>
                    </div>

                    <div class="col-12 col-md-2 mb-3 mb-md-0 col-md-2">
                        <label>Филиал</label>
                        <b-select v-model="selectedBranch">
                            <option :value="null">Все филиалы</option>
                            <option :key="branch.id" :value="branch" v-for="branch in branches">{{branch.name}}
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

                <div class="form-row form-group col-12">
                    <div class="col-12 col-md-2 mb-3 mb-md-0 col-md-2">
                        <label>Плательщик</label>
                        <b-select v-model="selectedBranchPayer">
                            <option :value="null">Все филиалы</option>
                            <option :key="branch.id" :value="branch" v-for="branch in branches">{{branch.name}}
                            </option>
                        </b-select>
                    </div>

                    <div class="col-12 col-md-2 mb-3 mb-md-0 col-md-2">
                        <label>Покупатель</label>
                        <b-select v-model="selectedBranchPayee">
                            <option :value="null">Все филиалы</option>
                            <option :key="branch.id" :value="branch" v-for="branch in branches">{{branch.name}}
                            </option>
                        </b-select>
                    </div>

                    <div class="col-12 col-md-2 mb-3 mb-md-0 col-md-2">
                        <label>Мин. сумма</label>
                        <input class="form-control" step="0.01" type="number" v-model.lazy="minPaidAmount">
                    </div>

                    <div class="col-12 col-md-2 mb-3 mb-md-0 col-md-2">
                        <label>Макс. сумма</label>
                        <input class="form-control" step="0.01" type="number" v-model.lazy="maxPaidAmount">
                    </div>

                    <div class="col-12 col-md-2 mb-3 mb-md-0 col-md-2" v-if="branches">
                        <label>Валюта</label>
                        <b-select v-model="selectedPaidCurrency">
                            <option :value="null">Все валюты</option>
                            <option :key="currency.id" :value="currency" v-for="currency in currencies">
                                {{currency.isoName}}
                            </option>
                        </b-select>
                    </div>

                    <div class="col-12 mb-3 mb-md-0 col-md-2">
                        <label>Кассир</label>
                        <search-user-dropdown :selected="cashierSelected"
                                              placeholder="Введите ФИО или код пользователя"
                                              url="/user/find?userInfo="/>
                    </div>

                    <div class="col-12 mb-3 mb-md-0 col-md-2">
                        <label>Статус</label>
                        <b-select v-model="selectedStatus">
                            <option :value="null">Все операции</option>
                            <option value="pending">Заявки</option>
                            <option value="completed">Проведенные</option>
                        </b-select>
                    </div>
                </div>
            </div>
        </template>

        <!--        <template slot="client.name" slot-scope="{item}">-->
        <!--            <a :href="'/profile/' + item.payer.id" v-if="item.payer">-->
        <!--                {{item.payer.name}}-->
        <!--            </a>-->
        <!--            <a :href="'/profile/' + item.recipient.id" v-if="item.recipient">-->
        <!--                {{item.recipient.name}}-->
        <!--            </a>-->
        <!--        </template>-->

        <template slot="billAmount" slot-scope="{item}">
            <span>{{item.billAmount}} {{item.billCurrency.isoName}}</span>
        </template>

        <template slot="paidAmount" slot-scope="{item}">
            <span>{{item.paidAmount}} {{item.paidCurrency.isoName}}</span>
        </template>

        <template slot="status" slot-scope="{item}">
            <span v-if="item.status === 'completed'">
                Проведенная
            </span>
            <span v-else>
                Заявка
            </span>
        </template>

        <template slot="show" slot-scope="{item}">
            <a class="btn" :href="'/payments/' + item.id">
                <img class="icon-btn-sm" src="/svg/file.svg">
            </a>
            <a v-if="item.status === 'pending'" class="btn" :href="'/payment/' + item.id + '/edit'">
                <img class="icon-btn-sm" src="/svg/edit.svg">
            </a>
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
            paymentItems: {
                type: Array
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
                customCells: ['show', 'billAmount', 'paidAmount', 'status'],
                selectedBranch: null,
                selectedType: null,
                selectedPaymentItem: null,
                selectedUserPayer: null,
                selectedUserPayee: null,
                selectedBranchPayer: null,
                selectedBranchPayee: null,
                selectedCashier: null,
                selectedPaidCurrency: null,
                selectedStatus:null,
                dateFrom: null,
                dateTo: null,
                minPaidAmount: null,
                maxPaidAmount: null,
                fields: {
                    created_at: {
                        label: 'Дата',
                        sortable: true
                    },
                    'payer.name': {
                        label: 'Плательщик',
                        sortable: true
                    },
                    'payee.name': {
                        label: 'Получатель',
                        sortable: true
                    },
                    'paymentItem.title': {
                        label: 'Статья',
                        sortable: true
                    },
                    billAmount: {
                        label: 'Выставленная Сумма',
                        sortable: true
                    },
                    paidAmount: {
                        label: 'Оплачено',
                        sortable: true
                    },

                    comment: {
                        label: "Комментарий"
                    },
                    'cashier.name': {
                        label: 'Кассир',
                        sortable: true
                    },
                    status:{
                        label: 'Статус',
                        sortable: true
                    },
                    'show': {
                        label: ''
                    }
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
                if (this.selectedPaymentItem)
                    action += 'item=' + this.selectedPaymentItem.id + '&';
                if (this.selectedUserPayer)
                    action += 'userPayer=' + this.selectedUserPayer.id + '&';
                if (this.selectedUserPayee)
                    action += 'userPayee=' + this.selectedUserPayee.id + '&';
                if (this.selectedBranchPayee)
                    action += 'branchPayee=' + this.selectedBranchPayee.id + '&';
                if (this.selectedBranchPayer)
                    action += 'branchPayer=' + this.selectedBranchPayer.id + '&';
                if (this.selectedCashier)
                    action += 'cashier=' + this.selectedCashier.id + '&';
                if (this.selectedPaidCurrency)
                    action += 'paidCurrency=' + this.selectedPaidCurrency.id + '&';
                if (this.dateFrom)
                    action += 'from=' + this.dateFrom + '&';
                if (this.dateTo)
                    action += 'to=' + this.dateTo + '&';
                if (this.minPaidAmount)
                    action += 'minPaidAmount=' + this.minPaidAmount + '&';
                if (this.maxPaidAmount)
                    action += 'maxPaidAmount=' + this.maxPaidAmount + '&';
                if (this.selectedStatus)
                    action += 'selectedStatus=' + this.selectedStatus + '&';
                return action;
            },
            payerSelected(user) {
                this.selectedUserPayer = user;
                this.getItems();
            },
            payeeSelected(user) {
                this.selectedUserPayee = user;
                this.getItems();
            },
            cashierSelected(cashier) {
                this.selectedCashier = cashier;
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
            selectedBranchPayee(){
                this.getItems();
            },
            selectedBranchPayer(){
                this.getItems();
            },
            selectedType() {
                this.getItems();
            },
            selectedPaymentItem() {
                this.getItems();
            },
            selectedPaidCurrency() {
                this.getItems();
            },
            dateFrom() {
                this.getItems();
            },
            dateTo() {
                this.getItems();
            },
            minPaidAmount() {
                this.getItems();
            },
            maxPaidAmount() {
                this.getItems();
            },
            selectedStatus(){
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
