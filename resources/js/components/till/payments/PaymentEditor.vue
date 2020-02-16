<template>
    <div class="container">
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
                            <b-form-checkbox v-b-popover.hover.top="'Физ. лицо'" switch class="mr-n2"
                                             v-model="isPayerIndividual">
                                <span class="sr-only">Switch for following text input</span>
                            </b-form-checkbox>
                        </b-input-group-prepend>
                        <b-input-group-prepend is-text>Плательщик</b-input-group-prepend>
                        <search-user-dropdown v-if="isPayerIndividual"
                                              v-model="payment.payer"
                                              :isInvalid="errors.payer"
                                              style="width:50%"
                                              :selected="onPayerSelected"></search-user-dropdown>
                        <b-form-select v-else v-model="payment.payer" :class="{'is-invalid':errors.payer}">
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
                            <b-form-checkbox switch v-b-popover.hover.top="'Физ. лицо'" class="mr-n2"
                                             v-model="isPayeeIndividual">
                                <span class="sr-only">Switch for following text input</span>
                            </b-form-checkbox>
                        </b-input-group-prepend>
                        <b-input-group-prepend is-text>Получатель</b-input-group-prepend>
                        <search-user-dropdown v-if="isPayeeIndividual"
                                              v-model="payment.payee"
                                              :isInvalid="errors.payee"
                                              :class="{'is-invalid':errors.payee}"
                                              style="width:51%"
                                              :selected="onPayeeSelected"></search-user-dropdown>
                        <b-form-select v-else v-model="payment.payee" :class="{'is-invalid':errors.payee}">
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
                        <b-form-input id="billAmount"
                                      v-model="payment.billAmount"
                                      :class="{'is-invalid':errors.billAmount}"
                                      class="form-control"
                                      type="number"></b-form-input>
                        <b-form-invalid-feedback :state="errors.billAmount"><strong
                            v-for="message in errors.billAmount">{{message}}</strong></b-form-invalid-feedback>
                    </div>

                    <div class="col-md-4 form-group">
                        <label for="billCurrency">Валюта</label>
                        <b-form-select id="billCurrency"
                                       v-model="payment.billCurrency"
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
                    <div class="col-md-4 form-group">
                        <label for="currentRate">Обменный курс</label>
                        <b-form-input id="currentRate"
                                      v-model="payment.exchangeRate"
                                      :class="{'is-invalid':errors.exchangeRate}"
                                      class="form-control"
                                      type="number"
                                      disabled></b-form-input>
                        <b-form-invalid-feedback :state="errors.exchangeRate"><strong
                            v-for="message in errors.exchangeRate">{{message}}</strong>
                        </b-form-invalid-feedback>
                    </div>

                    <div class="col-md-4 form-group">
                        <label for="paidAmount">Сумма</label>
                        <b-form-input id="paidAmount"
                                      v-model="payment.paidAmount"
                                      :class="{'is-invalid':errors.paidAmount}"
                                      class="form-control"
                                      type="number"
                                      disabled></b-form-input>
                        <b-form-invalid-feedback :state="errors.paidAmount"><strong
                            v-for="message in errors.paidAmount">{{message}}</strong></b-form-invalid-feedback>
                    </div>

                    <div class="col-md-4 form-group">
                        <label for="paidCurrency">Валюта оплаты</label>
                        <b-form-select id="paidCurrency"
                                       v-model="payment.paidCurrency"
                                       :class="{'is-invalid':errors.paidCurrency}">
                            <option :value="null" disabled>-- Выберите валюту --</option>
                            <option v-for="currency in currencies" :value="currency">
                                {{currency.name.charAt(0).toUpperCase() + currency.name.slice(1)}}
                                {{currency.isoName}}
                            </option>
                        </b-form-select>
                        <b-form-invalid-feedback :state="errors.paidCurrency"><strong
                            v-for="message in errors.paidCurrency">{{message}}</strong></b-form-invalid-feedback>
                    </div>
                </div>

                <div>
                    <b-form-textarea
                        id="comment"
                        v-model="payment.comment"
                        :class="{'is-invalid':errors.comment}"
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
            }
        },
        data() {
            return {
                isPayerIndividual: false,
                isPayeeIndividual: false,
                payment: {
                    status: 'pending',
                    payer: null,
                    payerType: null,
                    payee: null,
                    payeeType: null,
                    paymentItem: null,
                    billAmount: 0,
                    billCurrency: null,
                    exchangeRate: null,
                    paidAmount: 0,
                    paidCurrency: null,
                    comment: null
                },
                errors: {
                    id: null,
                    status: null,
                    payer: null,
                    payerType: null,
                    payee: null,
                    payeeType: null,
                    paymentItem: null,
                    billAmount: null,
                    billCurrency: null,
                    paidAmount: null,
                    paidCurrency: null,
                    comment: null,
                    exchangeRate: null
                }
            }
        },
        watch: {
            'payment.billAmount'() {
                this.payment.paidAmount = this.payment.billAmount;
            }
        },
        methods: {
            onPayerSelected(user) {
                this.payment.payer = user
            },
            onPayeeSelected(user) {
                this.payment.payee = user
            },
            async submit() {
                try {
                    let data = {
                        id: this.payment.id,
                        status: this.payment.status,
                        payer: this.payment.payer.id,
                        payerType: this.payment.payerType = this.isPayerIndividual ? 'user' : 'branch',
                        payee: this.payment.payee.id,
                        payeeType: this.payment.payerType = this.isPayeeIndividual ? 'user' : 'branch',
                        paymentItem: this.payment.paymentItem.id,
                        billAmount: this.payment.billAmount,
                        billCurrency: this.payment.billCurrency.id,
                        paidAmount: this.payment.paidAmount,
                        paidCurrency: this.payment.paidCurrency.id,
                        comment: this.payment.comment,
                        exchangeRate: this.payment.exchangeRate === null ? null : this.payment.exchangeRate.id
                    };

                    const response = await axios.post('/payment', data);
                } catch (e) {
                    if (e.response && e.response.status === 422) {
                        this.errors.id = e.response.data.errors.id;
                        this.errors.status = e.response.data.errors.status;
                        this.errors.payer = e.response.data.errors.payer;
                        this.errors.payerType = e.response.data.errors.payerType;
                        this.errors.payee = e.response.data.errors.payee;
                        this.errors.payeeType = e.response.data.errors.payeeType;
                        this.errors.paymentItem = e.response.data.errors.paymentItem;
                        this.errors.billAmount = e.response.data.errors.billAmount;
                        this.errors.billCurrency = e.response.data.errors.billCurrency;
                        this.errors.paidAmount = e.response.data.errors.paidAmount;
                        this.errors.paidCurrency = e.response.data.errors.paidCurrency;
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
