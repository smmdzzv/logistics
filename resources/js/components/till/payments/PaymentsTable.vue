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
        :setRowClass="rowClass"
        class="shadow"
        excelFileName="История платежей"
        excelSheetName="Все платежи"
        primaryKey="id"
        responsive>
        <template #header>
            <div class="form-row align-items-baseline">
                <div class="form-row form-group col-12 align-items-baseline">
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
                                {{ item.title }}
                            </option>
                        </b-select>
                    </div>

                    <div class="col-12 col-md-2 mb-3 mb-md-0 col-md-2">
                        <label>Филиал</label>
                        <b-select v-model="selectedBranch">
                            <option :value="null">Все филиалы</option>
                            <option :key="branch.id" :value="branch" v-for="branch in branches">{{ branch.name }}
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
                            <option :key="branch.id" :value="branch" v-for="branch in branches">{{ branch.name }}
                            </option>
                        </b-select>
                    </div>

                    <div class="col-12 col-md-2 mb-3 mb-md-0 col-md-2">
                        <label>Покупатель</label>
                        <b-select v-model="selectedBranchPayee">
                            <option :value="null">Все филиалы</option>
                            <option :key="branch.id" :value="branch" v-for="branch in branches">{{ branch.name }}
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
                                {{ currency.isoName }}
                            </option>
                        </b-select>
                    </div>

                    <div class="col-12 mb-3 mb-md-0 col-md-2">
                        <label>Кассир</label>
                        <search-user-dropdown :selected="cashierSelected"
                                              placeholder="Введите ФИО или код пользователя"
                                              url="/user/find?userInfo="/>
                    </div>
                </div>

                <b-row align-v="end" class="col-12">
                    <div class="col-12 mb-3  col-md-3">
                        <label>Статус</label>
                        <b-select v-model="selectedStatus">
                            <option :value="null">Все операции</option>
                            <option value="pending">Заявки</option>
                            <option value="completed">Проведенные</option>
                        </b-select>
                    </div>
                    <div class="col-12 col-md-3 mb-3">
                        <b-input-group>
                            <b-input-group-prepend is-text>
                                <b-form-checkbox switch class="mr-n2" v-model="withTrashed">
                                </b-form-checkbox>
                            </b-input-group-prepend>
                            <b-form-input disabled value="Показать удаленные"></b-form-input>
                        </b-input-group>
                    </div>
                    <div class="col-12 mb-3 col-md-6">
                        <b-input-group>
                            <b-input-group-prepend is-text>
                                <b-form-checkbox switch class="mr-n2" v-model="calculateCash">
                                </b-form-checkbox>
                            </b-input-group-prepend>
                            <b-form-input disabled value="Расчитать остаток средств"></b-form-input>
                        </b-input-group>
                    </div>
                </b-row>
            </div>
            <div class="row">
                <button class="btn btn-primary mx-auto" @click="getFilteredItems">Загрузить</button>
            </div>
            <div class="row col-12">
                <div v-if="calculateCash && cashReport" class="alert alert-primary w-100 mt-3">
                    <span>USD {{ cashReport.USD ? cashReport.USD : 0 }}</span>
                    <span>| TJS {{ cashReport.TJS ? cashReport.TJS : 0 }}</span>
                    <span>| RUB {{ cashReport.RUB ? cashReport.RUB : 0 }}</span>
                    <span>| CHY {{ cashReport.CHY ? cashReport.CHY : 0 }}</span>
                </div>
            </div>
        </template>

        <template v-slot:cell(billAmount)="{item}">
            <span>{{ item.billAmount }} {{ item.billCurrency.isoName }}</span>
        </template>

        <template v-slot:cell(paidAmount)="{item}">
            <span
                v-if="item.paidAmountInBillCurrency > 0">{{ item.paidAmountInBillCurrency.toFixed(2) }} {{ item.billCurrency.isoName }}</span>
            <span
                v-if="item.paidAmountInSecondCurrency > 0">{{ item.paidAmountInSecondCurrency.toFixed(2) }} {{ item.secondPaidCurrency.isoName }}</span>
        </template>

        <template v-slot:cell(status)="{item}">
            <span v-if="item.status === 'completed'">
                Проведенная
            </span>
            <span v-else>
                Заявка
                <span v-show="item.approved === null">рассматривается</span>
                <span v-show="item.approved === false">отклонена</span>
                <span v-show="item.approved">одобрена</span>
            </span>
        </template>

        <template v-slot:cell(show)="{item}">
            <a class="btn" :href="'/payment/' + item.id">
                <b-icon-file-earmark-text></b-icon-file-earmark-text>
            </a>
            <a v-if="item.status === 'pending' && !Boolean(item.deleted_at)" class="btn"
               :href="'/payment/' + item.id + '/edit'">
                <b-icon-pencil></b-icon-pencil>
            </a>
            <a v-if="Boolean(canApprove) && item.status === 'pending'" class="btn" href="#" @click.prevent="approvePayment(item)">
                <b-icon-file-earmark-check></b-icon-file-earmark-check>
            </a>
            <a v-if="!Boolean(item.deleted_at)" class="btn text-danger" href="#" @click.prevent="deletePayment(item)">
                <b-icon-trash></b-icon-trash>
            </a>
        </template>

        <template v-slot:cell(payer.name)="{item}">
            <template v-if="item.payer">
                <span v-if="item.payer_type === 'branch'">{{ item.payer.name }}</span>
                <span v-else>{{ item.payer.code }}</span>
            </template>
        </template>

        <template v-slot:cell(payee.name)="{item}">
            <template v-if="item.payee">
                <span v-if="item.payee_type === 'branch'">{{ item.payee.name }}</span>
                <span v-else>{{ item.payee.code }}</span>
            </template>
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
import {hideBusySpinner, showBusySpinner} from "../../../tools";

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
        },
        canApprove:Boolean
    },
    data() {
        return {
            pagination: {
                last_page: null,
                current_page: null
            },
            items: [],
            isBusy: false,
            selectedBranch: null,
            selectedType: null,
            selectedPaymentItem: null,
            selectedUserPayer: null,
            selectedUserPayee: null,
            selectedBranchPayer: null,
            selectedBranchPayee: null,
            selectedCashier: null,
            selectedPaidCurrency: null,
            selectedStatus: null,
            dateFrom: null,
            dateTo: null,
            minPaidAmount: null,
            maxPaidAmount: null,
            calculateCash: false,
            withTrashed: false,
            cashReport: null,
            fields: [
                {
                    key: 'number',
                    label: 'Чек'
                },
                {
                    key: 'created_at',
                    label: 'Дата',
                    sortable: true
                },
                {
                    key: 'payer.name',
                    label: 'Плательщик',
                    sortable: true
                },
                {
                    key: 'payee.name',
                    label: 'Получатель',
                    sortable: true
                },
                {
                    key: 'paymentItem.title',
                    label: 'Статья',
                    sortable: true
                },
                {
                    key: 'billAmount',
                    label: 'Выставленная Сумма',
                    sortable: true
                },
                {
                    key: 'paidAmount',
                    label: 'Оплачено',
                    sortable: true
                },

                {
                    key: 'comment',
                    label: "Комментарий"
                },
                {
                    key: 'creator.code',
                    label: 'Кассир',
                    sortable: true
                },
                {
                    key: 'status',
                    label: 'Статус',
                    sortable: true
                },
                {
                    key: 'branch.name',
                    label: 'Филиал',
                    sortable: true
                },
                {
                    key: 'show',
                    label: ''
                }
            ],
        }
    },
    methods: {
        prepareUrl(page) {
            let action = '/filtered-payments?';

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
            if (this.withTrashed)
                action += 'withTrashed=true&';
            let calcCash = page > 1 ? false : this.calculateCash;
            action += 'calculateCash=' + calcCash + '&';
            return action;
        },
        payerSelected(user) {
            this.selectedUserPayer = user;
        },
        payeeSelected(user) {
            this.selectedUserPayee = user;
        },
        cashierSelected(cashier) {
            this.selectedCashier = cashier;
        },
        getFilteredItems() {
            this.items = [];
            this.getItems()
        },
        getItems(page = 1) {
            if (this.trips)
                return;

            this.isBusy = true;

            let action = this.prepareUrl(page) + 'paginate=20&page=' + page;

            axios.get(action)
                .then(response => {
                    this.pagination = response.data;
                    if (this.flowable) {
                        for (let [key, value] of Object.entries(response.data.data)) {
                            if (key !== 'cashReport')
                                this.items.push(value);
                        }

                        if (page === 1) {
                            this.cashReport = response.data.data.cashReport;
                        }
                    } else
                        this.items = response.data.data;
                })
                .catch(e => {
                    console.log(e)
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
        approvePayment(payment) {
            this.$bvModal.msgBoxConfirm(
                'Одобрить заявку от ' + payment.created_at +
                ' за номером ' + payment.number,
                {
                    title: 'Статус заявки',
                    buttonSize: 'sm',
                    okVariant: 'success',
                    cancelVariant:'danger',
                    headerClass: 'p-2 border-bottom-0',
                    footerClass: 'p-2 border-top-0',
                    centered: true,
                    okTitle: 'Одобрить',
                    cancelTitle: 'Отклонить'
                })
                .then(confirmed => {
                    if(confirmed !== null){
                        showBusySpinner();
                        axios.put('/moderated-payments/' + payment.id, {approved: confirmed})
                            .then(_ => {
                                payment.approved = confirmed
                            });
                    }
                })
                .catch(err => {
                    // An error occurred
                })
                .finally(() => {
                    hideBusySpinner();
                })
        },
        deletePayment(payment) {
            this.$bvModal.msgBoxConfirm(
                'Удалить платеж от ' + payment.created_at +
                ' за номером ' + payment.number + ' ?',
                {
                    title: 'Подтверждение удаления',
                    size: 'sm',
                    buttonSize: 'sm',
                    okVariant: 'danger',
                    headerClass: 'p-2 border-bottom-0',
                    footerClass: 'p-2 border-top-0',
                    centered: true,
                    okTitle: 'Да',
                    cancelTitle: 'Отмена'
                })
                .then(confirm => {
                    if (confirm) {
                        showBusySpinner();
                        axios.delete('/payment/' + payment.id)
                            .then(_ => {
                                this.items = [];
                                this.getItems(
                                    this.pagination.current_page ? this.pagination.current_page : 1)
                            });
                    }
                })
                .catch(err => {
                    // An error occurred
                })
                .finally(() => {
                    hideBusySpinner();
                })
        },
        rowClass(item) {
            if (item && item.deleted_at)
                return 'table-danger'

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
