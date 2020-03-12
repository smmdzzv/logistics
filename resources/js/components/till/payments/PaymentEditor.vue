<template>
    <div class="container">
        <div class="alert alert-info" v-if="payerAccounts">
            <span>{{payment.payer.name}}  <span v-for="account in payerAccounts">
                {{account.balance}} {{account.currency.isoName}} | </span>
            </span>
        </div>
        <div class="card">
            <div class="card-header">
                Провести платеж
            </div>
            <div class="card-body">
                <div class="row">
                    <!--                    <div class="col-md-6 form-group">-->
                    <!--                        <label for="type">Тип операции</label>-->
                    <!--                        <b-form-select id="type" class="form-control">-->
                    <!--                            <option :value="null" disabled>&#45;&#45; Выберите тип операции &#45;&#45;</option>-->
                    <!--                        </b-form-select>-->
                    <!--                    </div>-->
                    <div class="col-md-12 form-group">
                        <label for="status">Статус</label>
                        <b-form-select id="status"
                                       class="form-control"
                                       v-model="payment.status"
                                       :class="{'is-invalid':errors.status}">
                            <option :value="null" disabled>-- Выберите статус операции --</option>
                            <option value="pending">ЗАЯВКА</option>
                            <option value="completed">ПРОВЕДЕННАЯ</option>
                        </b-form-select>
                        <b-form-invalid-feedback :state="errors.status"><strong
                            v-for="message in errors.status"> {{message}}</strong>
                        </b-form-invalid-feedback>
                    </div>
                </div>

                <div class="row mb-3">
                    <!-- PAYER -->
                    <b-input-group class="col-md-6">
                        <b-input-group-prepend is-text>
                            <b-form-checkbox v-b-popover.hover.top="'Физ. лицо'"
                                             switch class="mr-n2"
                                             :disabled="disable"
                                             v-model="isPayerIndividual">
                                <span class="sr-only">Switch for following text input</span>
                            </b-form-checkbox>
                        </b-input-group-prepend>
                        <b-input-group-prepend is-text>Плательщик</b-input-group-prepend>
                        <search-user-dropdown v-if="isPayerIndividual"
                                              v-model="payment.payer"
                                              :preselectedUser="payment.payer"
                                              :isInvalid="errors.payer"
                                              :errorMessages="errors.payer"
                                              :disabled="disable"
                                              style="width:50%"
                                              :selected="onPayerSelected"
                        >
                        </search-user-dropdown>
                        <b-form-select v-else :disabled="disable" v-model="payment.payer"
                                       :class="{'is-invalid':errors.payer}">
                            <option :value="null" disabled>-- Выберите филиал --</option>
                            <option v-for="branch in branches" :value="branch">{{branch.name}}</option>
                        </b-form-select>
                        <span class="invalid-feedback" role="alert" v-if="errors.payer">
                                             <strong v-for="message in errors.payer">{{message}}</strong>
                                        </span>
                    </b-input-group>

                    <!-- PAYEE -->
                    <b-input-group class="col-md-6">
                        <b-input-group-prepend is-text>
                            <b-form-checkbox switch v-b-popover.hover.top="'Физ. лицо'"
                                             :disabled="disable"
                                             class="mr-n2"
                                             v-model="isPayeeIndividual">
                                <span class="sr-only">Switch for following text input</span>
                            </b-form-checkbox>
                        </b-input-group-prepend>
                        <b-input-group-prepend is-text>Получатель</b-input-group-prepend>
                        <search-user-dropdown v-if="isPayeeIndividual"
                                              v-model="payment.payee"
                                              :preselectedUser="payment.payer"
                                              :isInvalid="errors.payee"
                                              :errorMessages="errors.payee"
                                              :class="{'is-invalid':errors.payee}"
                                              style="width:51%"
                                              :selected="onPayeeSelected"></search-user-dropdown>
                        <b-form-select v-else :disabled="disable" v-model="payment.payee"
                                       :class="{'is-invalid':errors.payee}">
                            <option :value="null" disabled>-- Выберите филиал --</option>
                            <option v-for="branch in branches" :value="branch">{{branch.name}}</option>
                        </b-form-select>
                        <b-form-invalid-feedback :state="errors.payee"><strong
                            v-for="message in errors.payee">{{message}}</strong></b-form-invalid-feedback>
                    </b-input-group>
                </div>

                <div class="row">
                    <div class="col-md-4 form-group">
                        <label for="paymentItem">Статья</label>
                        <b-form-select id="paymentItem" v-model="payment.paymentItem"
                                       :class="{'is-invalid':errors.paymentItem}"
                                       :disabled="disable"
                                       class="form-control">
                            <option :value="null" disabled>-- Выберите статью --</option>
                            <option v-for="paymentItem in paymentItems" :value="paymentItem">
                                {{paymentItem.title}}
                            </option>
                        </b-form-select>
                        <b-form-invalid-feedback :state="errors.paymentItem"><strong
                            v-for="message in errors.paymentItem">{{message}}</strong>
                        </b-form-invalid-feedback>
                    </div>

                    <div class="col-md-4 form-group">
                        <label for="billAmount">Сумма</label>
                        <input id="billAmount"
                               v-model.lazy="payment.billAmount"
                               class="form-control"
                               :class="{'is-invalid':errors.billAmount}"
                               :disabled="disable"
                               type="number">
                        <b-form-invalid-feedback :state="errors.billAmount"><strong
                            v-for="message in errors.billAmount">{{message}}</strong></b-form-invalid-feedback>
                    </div>

                    <div class="col-md-4 form-group">
                        <label for="billCurrency">Валюта</label>
                        <b-form-select id="billCurrency"
                                       v-b-tooltip.hover
                                       title="Валюта в которой определяется необходимая сумма платежа.
                                       Для некоторых статей оплаты определяет счет зачисления.
                                       Наример, при обмене валют определяет желаемую клиентом валюту. "
                                       v-model="payment.billCurrency"
                                       :disabled="disable"
                                       :class="{'is-invalid':errors.billCurrency}">
                            <option :value="null" disabled>-- Выберите валюту --</option>
                            <option v-for="currency in currencies" :value="currency">
                                {{currency.name.charAt(0).toUpperCase() + currency.name.slice(1)}}
                                {{currency.isoName}}
                            </option>
                        </b-form-select>
                        <b-form-invalid-feedback :state="errors.billCurrency"><strong
                            v-for="message in errors.billCurrency">{{message}}</strong></b-form-invalid-feedback>
                    </div>
                </div>

                <div class="row my-3">
                    <h4 class="m-auto">Сумма к оплате</h4>
                </div>

                <div class="row">
                    <div class="col-md-3 form-group">
                        <label for="paidAmount">Сумма</label>
                        <input id="paidAmountInBillCurrency"
                               v-model.lazy="payment.paidAmountInBillCurrency"
                               :class="{'is-invalid':errors.paidAmountInBillCurrency}"
                               class="form-control"
                               type="number">
                        <b-form-invalid-feedback :state="errors.paidAmountInBillCurrency"><strong
                            v-for="message in errors.paidAmountInBillCurrency">{{message}}</strong>
                        </b-form-invalid-feedback>
                    </div>

                    <div class="col-md-3 form-group">
                        <label for="paymentCurrency">Валюта оплаты</label>
                        <b-form-select id="paymentCurrency"
                                       class="dummy"
                                       v-b-tooltip.hover
                                       title="Валюта в которой принимаются деньги у клиента. Валюта оплаты также определяет счет списания."
                                       v-model="payment.billCurrency"
                                       disabled>
                            <option :value="null" disabled>-- Выберите валюту --</option>
                            <option v-for="currency in currencies" :value="currency">
                                {{currency.name.charAt(0).toUpperCase() + currency.name.slice(1)}}
                                {{currency.isoName}}
                            </option>
                        </b-form-select>
                    </div>

                    <div class="col-md-3 form-group">
                        <label for="paidAmountInSecondCurrency">Сумма</label>
                        <b-input-group>
                            <template v-slot:prepend>
                                <b-input-group-text>
                                    <strong v-if="payment.exchangeRate">{{payment.exchangeRate.coefficient}}</strong>
                                </b-input-group-text>
                            </template>
                            <input id="paidAmountInSecondCurrency"
                                   v-model.lazy="payment.paidAmountInSecondCurrency"
                                   :class="{'is-invalid':errors.paidAmountInSecondCurrency}"
                                   class="form-control"
                                   type="number">
                            <b-form-invalid-feedback :state="errors.paidAmountInSecondCurrency"><strong
                                v-for="message in errors.paidAmountInSecondCurrency">{{message}}</strong>
                            </b-form-invalid-feedback>
                        </b-input-group>
                    </div>

                    <div class="col-md-3 form-group">
                        <label for="secondPaidCurrency">Доп. валюта оплаты</label>
                        <b-form-select id="secondPaidCurrency"
                                       v-b-tooltip.hover
                                       title="Валюта в которой принимаются деньги у клиента. Валюта оплаты также определяет счет списания."
                                       v-model="payment.secondPaidCurrency"
                                       :class="{'is-invalid':errors.secondPaidCurrency}">
                            <option :value="null" disabled>-- Выберите валюту --</option>
                            <option v-for="currency in currencies" :value="currency"
                                    :disabled="payment.billCurrency && currency.id === payment.billCurrency.id">
                                {{currency.name.charAt(0).toUpperCase() + currency.name.slice(1)}}
                                {{currency.isoName}}
                            </option>
                        </b-form-select>
                        <b-form-invalid-feedback :state="errors.secondPaidCurrency"><strong
                            v-for="message in errors.secondPaidCurrency">{{message}}</strong></b-form-invalid-feedback>
                    </div>
                </div>

                <div>
                    <b-form-textarea
                        id="comment"
                        v-model="payment.comment"
                        :class="{'is-invalid':errors.comment}"
                        :disabled="disable"
                        placeholder="Введите комментарий"
                        rows="3"
                        max-rows="6"
                    ></b-form-textarea>
                    <b-form-invalid-feedback :state="errors.comment"><strong
                        v-for="message in errors.comment">{{message}}</strong></b-form-invalid-feedback>
                </div>

                <div class="row my-4">
                    <button class="btn btn-primary mx-auto" @click="submit">Сохранить</button>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        name: "PaymentEditor",
        mounted() {
            if (this.providedPayment) {
                this.pausePayerWatcher = true;
                this.pausePayeeWatcher = true;
                this.isPayerIndividual = this.providedPayment.payer_type === 'user';
                this.isPayeeIndividual = this.providedPayment.payee_type === 'user';
                this.payment = this.providedPayment
            }
        },
        props: {
            branches: {
                type: Array,
                required: true
            },
            currencies: {
                type: Array,
                required: true
            },
            paymentItems: {
                type: Array,
                required: true
            },
            providedPayment: {
                type: Object
            },
            disable: {
                type: Boolean,
                default: false
            }
        },
        data() {
            return {
                isPayerIndividual: false,
                isPayeeIndividual: false,
                payment: {
                    status: 'pending',
                    payer: null,
                    payer_type: null,
                    payee: null,
                    payee_type: null,
                    paymentItem: null,
                    billAmount: 0,
                    billCurrency: null,
                    exchangeRate: null,
                    paidAmountInBillCurrency: 0,
                    paidAmountInSecondCurrency: 0,
                    secondPaidCurrency: null,
                    comment: null
                },
                payerAccounts: null,
                pausePayerWatcher: false,
                errors: {
                    id: null,
                    status: null,
                    payer: null,
                    payer_type: null,
                    payee: null,
                    payee_type: null,
                    paymentItem: null,
                    billAmount: null,
                    billCurrency: null,
                    paidAmountInBillCurrency: null,
                    paidAmountInSecondCurrency: null,
                    secondPaidCurrency: null,
                    comment: null,
                    exchangeRate: null
                }
            }
        },
        watch: {
            'payment.billAmount'() {
                this.calculatePaidInBillCurrencyAmount();
            },
            'payment.paidAmountInSecondCurrency'() {
                this.calculatePaidInBillCurrencyAmount();
            },
            'payment.paidAmountInBillCurrency'() {
                this.calculatePaidInSecondCurrencyAmount();
            },
            'payment.billCurrency'() {
                this.checkIfNeededConverting()
            },
            'payment.secondPaidCurrency'() {
                this.checkIfNeededConverting()
            },
            'payment.exchangeRate'() {
                this.calculatePaidInBillCurrencyAmount();
            },
            isPayerIndividual() {
                if (!this.pausePayerWatcher)
                    this.payment.payer = null;
                else
                    this.pausePayerWatcher = false;
            },
            isPayeeIndividual() {
                if (!this.pausePayeeWatcher)
                    this.payment.payee = null;
                else
                    this.pausePayeeWatcher = false;
            },
            'payment.payer'() {
                this.payerAccounts = null;
                this.getPayerAccounts();
            }
        },
        methods: {
            calculatePaidInSecondCurrencyAmount() {
                if (this.payment.secondPaidCurrency && this.payment.exchangeRate)
                    this.payment.paidAmountInSecondCurrency = Math.round((this.payment.billAmount - this.payment.paidAmountInBillCurrency) * this.payment.exchangeRate.coefficient * 100) / 100;
                else {
                    this.payment.paidAmountInBillCurrency = this.payment.billAmount;
                    this.payment.paidAmountInSecondCurrency = 0;
                }
            },
            calculatePaidInBillCurrencyAmount() {
                if (this.payment.secondPaidCurrency && this.payment.exchangeRate && this.payment.paidAmountInSecondCurrency > 0)
                    this.payment.paidAmountInBillCurrency = Math.round((this.payment.billAmount - this.payment.paidAmountInSecondCurrency / this.payment.exchangeRate.coefficient) * 100) / 100;
                else
                    this.payment.paidAmountInBillCurrency = this.payment.billAmount;
            },
            checkIfNeededConverting() {
                let needConverting = this.payment.billCurrency && this.payment.secondPaidCurrency && this.payment.billCurrency.id !== this.payment.secondPaidCurrency.id;

                if (needConverting)
                    this.getExchangeRate();
                else
                    this.payment.exchangeRate = null;
            },
            onPayerSelected(user) {
                this.payment.payer = user

            },
            onPayeeSelected(user) {
                this.payment.payee = user
            },
            async getExchangeRate() {
                tShowSpinner();
                let action = `exchange-history/rate/${this.payment.secondPaidCurrency.id}/${this.payment.billCurrency.id}`;
                try {
                    const result = await axios.get(action);
                    this.payment.exchangeRate = result.data;
                } catch (e) {
                    this.$root.showErrorMsg('Ошибка загрузки',
                        'Не удалось загрузить курс валют. Убедитесь, что курс для данной валюты создан.')
                    this.payment.exchangeRate = {coefficient: null};
                }

                this.$nextTick(
                    () => {
                        tHideSpinner();
                    }
                )
            },
            async getPayerAccounts() {
                if (!this.payment.payer)
                    return;

                try {
                    let action = '/accounts/' + this.payment.payer.id;
                    const response = await axios.get(action);
                    this.payerAccounts = response.data;
                } catch (e) {
                    this.$root.showErrorMsg('Ошибка загрузки',
                        'Не удалось загрузить информацию о счетах плательщика')
                }
            },
            async submit() {
                try {
                    let data = {
                        id: this.payment.id,
                        status: this.payment.status,
                        payer: this.payment.payer.id,
                        payer_type: this.payment.payer_type = this.isPayerIndividual ? 'user' : 'branch',
                        payee: this.payment.payee.id,
                        payee_type: this.payment.payer_type = this.isPayeeIndividual ? 'user' : 'branch',
                        paymentItem: this.payment.paymentItem.id,
                        billAmount: this.payment.billAmount,
                        billCurrency: this.payment.billCurrency.id,
                        paidAmountInBillCurrency: this.payment.paidAmountInBillCurrency,
                        paidAmountInSecondCurrency: this.payment.paidAmountInSecondCurrency,
                        secondPaidCurrency: this.payment.secondPaidCurrency ? this.payment.secondPaidCurrency.id : null,
                        comment: this.payment.comment,
                        exchangeRate: this.payment.exchangeRate === null ? null : this.payment.exchangeRate.id
                    };

                    const response = await axios.post('/payment', data);
                    window.location.href = '/payment/' + response.data;
                } catch (e) {console.log(e);
                    if (e.response && e.response.status === 422) {
                        this.errors.id = e.response.data.errors.id;
                        this.errors.status = e.response.data.errors.status;
                        this.errors.payer = e.response.data.errors.payer;
                        this.errors.payer_type = e.response.data.errors.payer_type;
                        this.errors.payee = e.response.data.errors.payee;
                        this.errors.payee_type = e.response.data.errors.payee_type;
                        this.errors.paymentItem = e.response.data.errors.paymentItem;
                        this.errors.billAmount = e.response.data.errors.billAmount;
                        this.errors.billCurrency = e.response.data.errors.billCurrency;
                        this.errors.paidAmountInBillCurrency = e.response.data.errors.paidAmountInBillCurrency;
                        this.errors.paidAmountInSecondCurrency = e.response.data.errors.paidAmountInSecondCurrency;
                        this.errors.secondPaidCurrency = e.response.data.errors.secondPaidCurrency;
                        this.errors.comment = e.response.data.errors.comment;
                        this.errors.exchangeRate = e.response.data.errors.exchangeRate;
                    } else {
                        this.$root.showErrorMsg('Ошибка соранения',
                            'Не удалось провести платеж. Обновите странице и повторите попытку')
                    }
                }
            }
        },
        components: {
            'SearchUserDropdown': require('../../users/SearchUserDropdown.vue').default
        }
    }
</script>

<style scoped>

</style>
