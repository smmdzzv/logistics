<template>
    <div class="container">
        <form @submit.prevent="submitForm">
            <div class="row justify-content-center">
                <div class="col-md-12 col-lg-10">
                    <div class="card shadow">
                        <div class="card-header">
                            Проведение платежа
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="form-group col-md-6">
                                    <label>Тип операции</label>
                                    <b-form-select disabled v-model="paymentType">
                                        <option value="in">ПРИХОД</option>
                                    </b-form-select>
                                </div>

                                <div class="form-group col-md-6">
                                    <label>Статус операции</label>
                                    <b-form-select id="status" v-model="status">
                                        <option value="pending">ЗАЯВКА</option>
                                        <option value="completed">ПРОВЕДЕННАЯ</option>
                                    </b-form-select>
                                </div>
                            </div>

                            <div class="row">
                                <div class="form-row col-md-6">
                                    <div
                                        :class="{'col-md-8': clientTotalDebt !== null, 'col-md-12': clientTotalDebt === null}"
                                        class="form-group">
                                        <label for="client">Плательщик</label>
                                        <search-user-dropdown :isInvalid="$v.client.$error || errors.client"
                                                              :preselectedUser="client"
                                                              :selected="clientSelected"
                                                              placeholder="Введите ФИО или код клиента"
                                                              url="/concrete/client/filter?userInfo="></search-user-dropdown>
                                        <input class="is-invalid form-control" id="client" type="hidden">
                                        <span class="invalid-feedback" role="alert"
                                              v-if="$v.client.$error || errors.client">
                                        <strong>Необходимо выбрать клиента.</strong>
                                        <strong v-for="message in errors.client">{{message}}.</strong>
                                    </span>
                                    </div>

                                    <div class="col-md-4" v-if="clientTotalDebt !== null">
                                        <label for="debt">Задолженность</label>
                                        <input :value="clientTotalDebt" class="form-control" disabled id="debt"
                                               type="text">
                                    </div>
                                </div>

                                <div class="form-group col-md-6">
                                    <label>Счет зачисления</label>
                                    <b-form-select id="accountTo" v-model="accountTo">
                                        <option :value="null">-- Выберите счет зачисления --</option>
                                        <option :key="accountTo.id" :value="accountTo" v-for="accountTo in accountsTo">
                                            {{accountTo.description}}
                                        </option>
                                    </b-form-select>
                                </div>

                                <span class="invalid-feedback" role="alert" v-if="$v.accountTo.$error">
                                    <strong>Необходимо выбрать </strong>
                                </span>

                                <span class="invalid-feedback" role="alert" v-if="errors.accountTo">
                                     <strong v-for="message in errors.accountTo">{{message}}</strong>
                                </span>
                            </div>

                            <div class="row">
                                <div class="form-group col-md-6">
                                    <label>Статья</label>
                                    <b-form-select :class="{'is-invalid':$v.paymentItem.$error  || errors.paymentItem}"
                                                   :disabled="client === null || disable"
                                                   v-model="paymentItem">
                                        <template v-slot:first>
                                            <option :value="null" disabled>
                                                -- Выберите статью
                                                <span v-if="paymentType === 'in'">прихода</span>
                                                <span v-else>расхода</span>
                                                --
                                            </option>
                                        </template>
                                        <option :key="paymentItem.id" :value="paymentItem"
                                                v-for="paymentItem in paymentItems">
                                            {{paymentItem.title.toUpperCase()}}
                                        </option>
                                    </b-form-select>

                                    <span class="invalid-feedback" role="alert" v-if="$v.paymentItem.$error">
                                        <strong>Необходимо выбрать статью
                                            <span v-if="paymentType === 'in'">прихода</span>
                                                <span v-else>расхода</span>
                                        </strong>
                                    </span>

                                    <span class="invalid-feedback" role="alert" v-if="errors.paymentItem">
                                         <strong v-for="message in errors.paymentItem">{{message}}</strong>
                                    </span>
                                </div>

                                <div class="form-group col-6 col-md-4">
                                    <label for="amount">Сумма</label>
                                    <input :class="{'is-invalid':$v.amount.$error  || errors.amount}"
                                           class="form-control"
                                           id="amount"
                                           type="number"
                                           v-model.number="amount">

                                    <span class="invalid-feedback" role="alert" v-if="$v.amount.$error">
                                        <strong>Сумма платежа должна быть отличной от нуля</strong>
                                    </span>

                                    <span class="invalid-feedback" role="alert" v-if="errors.amount">
                                         <strong v-for="message in errors.amount">{{message}}</strong>
                                    </span>
                                </div>

                                <div class="form-group col-6 col-md-2">
                                    <label>Валюта</label>
                                    <b-form-select :class="{'is-invalid':$v.currency.$error  || errors.currency}"
                                                   v-model="currency">
                                        <option :key="currency.id" :value="currency" v-for="currency in currencies">
                                            {{currency.name.toUpperCase()}}
                                        </option>
                                    </b-form-select>

                                    <span class="invalid-feedback" role="alert" v-if="$v.currency.$error">
                                        <strong>Необходимо выбрать валюту</strong>
                                    </span>

                                    <span class="invalid-feedback" role="alert" v-if="errors.currency">
                                         <strong v-for="message in errors.currency">{{message}}</strong>
                                    </span>
                                </div>
                            </div>

                            <div class="row">
                                <!--                                <div class="col-md-6" v-if="isOrderPayment">-->
                                <!--                                    <b-form-group label="Заказ" label-for="order">-->
                                <!--                                        <b-form-select-->
                                <!--                                            :class="{'is-invalid':$v.order.$error  || errors.order}"-->
                                <!--                                            id="order"-->
                                <!--                                            v-model="order">-->
                                <!--                                            <template v-slot:first>-->
                                <!--                                                <option :value="null">-->
                                <!--                                                    <span v-if="orders.length > 0">&#45;&#45;Выберите заказ&#45;&#45;</span>-->
                                <!--                                                    <span v-else>&#45;&#45;Заказов нет&#45;&#45;</span>-->
                                <!--                                                </option>-->
                                <!--                                            </template>-->

                                <!--                                            <option :key="order.id" :value="order"-->
                                <!--                                                    v-for="order in orders">-->
                                <!--                                                {{order.totalPrice}} USD {{order.created_at}}-->
                                <!--                                            </option>-->
                                <!--                                        </b-form-select>-->

                                <!--                                        <span class="invalid-feedback" role="alert" v-if="$v.order.$error">-->
                                <!--                                            <strong>Необходимо выбрать заказ</strong>-->
                                <!--                                        </span>-->

                                <!--                                        <span class="invalid-feedback" role="alert" v-if="errors.order">-->
                                <!--                                             <strong v-for="message in errors.order">{{message}}</strong>-->
                                <!--                                        </span>-->
                                <!--                                    </b-form-group>-->
                                <!--                                </div>-->

                                <div class="col-md-3" v-show="needConverting">
                                    <b-form-group
                                        id="rate"
                                        label="Курс"
                                        label-for="rate">
                                        <b-form-input :class="{'is-invalid': errors.exchange}" disabled
                                                      id="rate"
                                                      v-model="exchange.coefficient"></b-form-input>

                                        <span class="invalid-feedback" role="alert" v-if="errors.exchange">
                                             <strong v-for="message in errors.exchange">{{message}}</strong>
                                        </span>
                                    </b-form-group>
                                </div>

                                <div class="col-md-3" v-show="needConverting">
                                    <b-form-group
                                        id="convertedAmount"
                                        label="Сумма зачисления"
                                        label-for="convertedAmount">
                                        <b-input-group :append="exchange.toCurrency.isoName">
                                            <b-form-input disabled id="convertedAmount"
                                                          v-model="convertedAmount"></b-form-input>

                                        </b-input-group>
                                    </b-form-group>
                                </div>
                            </div>

                            <div class="row">
                                <div class="form-group col-12">
                                    <label for="comment">Комментарий</label>
                                    <input class="form-control" id="comment"
                                           placeholder="Добавьте произвольный комментарий (необязательно)" type="text"
                                           v-model="comment">
                                </div>
                            </div>
                        </div>
                        <div class="card-footer">
                            <div class="form-group row mb-0">
                                <div class="col-md-6 offset-md-4">
                                    <button class="btn btn-primary" type="submit">
                                        Провести операцию
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</template>

<script>
    import {decimal, required} from 'vuelidate/lib/validators';

    const validateAmount = (value, vm) => {
        if (vm.requiredAmount) {
            let compareTo = vm.needConverting ? vm.convertedAmount : vm.amount;
            console.log(compareTo, vm.requiredAmount);
            return vm.requiredAmount === compareTo;
        } else
            return vm.amount > 0;
    };

    export default {
        name: "IncomingPaymentEditor",
        mounted() {
            if (this.currencies)
                this.currency = this.currencies[0];

            if (this.payment)
                this.setInitialData();
            else
                this.paymentType = 'in';

            if (this.disable)
                this.disableForm()
        },
        props: {
            accountsTo: {
                type: Array,
                required: false
            },
            currencies: {
                type: Array,
                required: false
            },
            payment: {
                type: Object
            },
            disable: {
                type: Boolean,
                default: false
            }
        },
        data() {
            return {
                client: null,
                clientTotalDebt: null,
                amount: 0,
                currency: null,
                paymentItem: null,
                paymentType: null,
                comment: null,
                needConverting: false,
                orders: [],
                order: null,
                requiredAmount: null,
                accountTo: null,
                exchange: {
                    coefficient: null,
                    toCurrency: {
                        isoName: null
                    }
                },
                status: 'pending',
                errors: {
                    client: null,
                    amount: null,
                    currency: null,
                    paymentItem: null,
                    order: null,
                    exchange: null,
                    status: null
                },
                paymentItems: []
            }
        },
        watch: {
            paymentType() {
                if (this.disable)
                    return;

                this.$bvModal.show('busyModal');
                // tShowSpinner();
                this.requiredAmount = null;
                this.paymentItem = null;
                this.order = null;

                let action = '/payment-items/type/' + this.paymentType;
                axios.get(action)
                    .then(
                        response => {
                            this.paymentItems = response.data;
                            if (this.paymentItems.length === 1)
                                this.paymentItem = this.paymentItems[0];
                        }
                    ).catch();

                this.$nextTick(
                    () => {
                        this.$bvModal.hide('busyModal');
                        // tHideSpinner();
                    }
                )
            },
            accountTo() {
                this.convertIfNeeded();
            },
            currency() {
                this.convertIfNeeded();
            }
        },
        computed: {
            convertedAmount() {
                let amount = this.amount * this.exchange.coefficient;
                return amount.toFixed(2);
            },
        },
        methods: {
            convertIfNeeded() {
                this.needConverting = this.accountTo && this.currency.id !== this.accountTo.currency.id;
                if (this.needConverting)
                    this.convert()
            },
            clientSelected(client) {
                if (this.client && client && this.client.id === client.id)
                    return;

                this.client = client;
                this.getClientTotalDebt();
            },
            async getClientTotalDebt() {
                if (!this.client)
                {
                    this.clientTotalDebt = null;
                    return;
                }


                try {
                    let action = "/orders/" + this.client.id + "/debt";
                    const response = await axios.get(action);
                    this.clientTotalDebt = response.data + " USD";
                } catch (e) {
                    console.log(e)
                    this.$root.showErrorMsg('Ошибка загрузки',
                        'Не удалось загрузить задолженность клиента.')
                }
            },
            async convert() {
                // tShowSpinner();
                if (this.disable)
                    return;

                let action = `exchange-history/rate/${this.currency.id}/${this.accountTo.currency.id}`;
                try {
                    const result = await axios.get(action);
                    this.exchange = result.data;
                } catch (e) {
                    this.$root.showErrorMsg('Ошибка загрузки',
                        'Не удалось загрузить курс валют. Убедитесь, что курс для данной валюты создан.')
                }

                // this.$nextTick(
                //     () => {
                //         tHideSpinner();
                //     }
                // )
            },
            setInitialData() {
                this.currencies = [this.payment.currency];
                this.paymentItems = [this.payment.paymentItem];
                this.accountsTo = [this.payment.accountTo];
                this.paymentType = this.payment.paymentItem.type;

                this.client = this.payment.payer;
                this.paymentItem = this.payment.paymentItem;
                this.status = this.payment.status;
                this.currency = this.payment.currency;
                this.accountTo = this.payment.accountTo;
                this.amount = this.payment.amount;
                if (this.payment.exchange)
                    this.exchange = this.payment.exchange;
                this.comment = this.payment.comment;
            },
            disableForm() {
                $("input").prop("disabled", true);
                $("select").prop("disabled", true);
                $("#status").prop("disabled", false);
            },
            async submitForm() {
                this.$bvModal.show('busyModal');
                if (this.$v.$invalid)
                    this.$v.$touch();
                else {
                    let data = {
                        payerId: this.client.id,
                        paymentItemId: this.paymentItem.id,
                        status: this.status,
                        currencyId: this.currency.id,
                        accountTo: this.accountTo.id,
                        amount: this.amount,
                        exchangeId: this.exchange.id,
                        // orderId: this.order ? this.order.id : null,
                        comment: this.comment,
                        id: this.payment ? this.payment.id : null
                    };

                    try {
                        // const result = await axios.post('/incoming-payments', data)
                        if (this.payment)
                            await axios.patch('/incoming-payments/' + this.payment.id, data);
                        else
                            await axios.post('/incoming-payments', data);

                        window.location.href = '/payments'
                    } catch (e) {
                        if (e.response.status === 422) {
                            this.errors.client = e.response.data.errors.payerId;
                            this.errors.order = e.response.data.errors.orderId;
                            this.errors.amount = e.response.data.errors.amount;
                            this.errors.currency = e.response.data.errors.currencyId;
                            this.errors.paymentItem = e.response.data.errors.paymentItemId;
                            this.errors.exchange = e.response.data.errors.exchangeId;
                            this.errors.status = e.response.data.errors.status;
                        } else {
                            this.$root.showErrorMsg('Ошибка соранения',
                                'Не удалось провести платеж. Обновите странице и повторите попытку')
                        }
                    }
                }

                this.$nextTick(() => {
                        this.$bvModal.hide('busyModal');
                    }
                );
            },
        },
        components: {
            'SearchUserDropdown': require('../../../users/SearchUserDropdown.vue').default,
        },
        validations: {
            client: {
                required
            },
            currency: {
                required
            },
            paymentItem: {
                required
            },
            amount: {
                required,
                decimal,
                validateAmount
            },
            // order: {
            //     validateOrder
            // },
            accountTo: {
                required
            }
        }
    }
</script>

<style scoped>

</style>
