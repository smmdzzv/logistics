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
                                <div class="form-group col-md-12">
                                    <label>Тип операции</label>
                                    <b-form-select disabled v-model="paymentType">
                                        <option value="in">ПРИХОД</option>
                                    </b-form-select>
                                </div>
                            </div>

                            <div class="row">
                                <div class="form-group col-md-6">
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
                                <div class="form-group col-md-6">
                                    <label for="accountTo">Счет зачисления</label>
                                    <input class="form-control" disabled id="accountTo" type="text"
                                           v-model="accountTo.description">
                                </div>

                                <!--                        <span class="invalid-feedback" role="alert" v-if="$v.accountTo.$error">-->
                                <!--                            <strong>Необходимо выбрать </strong>-->
                                <!--                        </span>-->

                                <!--                        <span class="invalid-feedback" role="alert" v-if="errors.accountTo">-->
                                <!--                             <strong v-for="message in errors.accountTo">{{message}}</strong>-->
                                <!--                        </span>-->
                            </div>

                            <div class="row">
                                <div class="form-group col-md-6">
                                    <label>Статья</label>
                                    <b-form-select :class="{'is-invalid':$v.paymentItem.$error  || errors.paymentItem}"
                                                   :disabled="client === null"
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
                                        <strong v-if="isOrderPayment">Сумма платежа должна быть отличной от нуля и равна сумме заказа</strong>
                                        <strong v-else>Сумма платежа должна быть отличной от нуля</strong>
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
                                <div class="col-md-6" v-if="isOrderPayment">
                                    <b-form-group label="Заказ" label-for="order">
                                        <b-form-select
                                            :class="{'is-invalid':$v.order.$error  || errors.order}"
                                            id="order"
                                            v-model="order">
                                            <template v-slot:first>
                                                <option :value="null">
                                                    <span v-if="orders.length > 0">--Выберите заказ--</span>
                                                    <span v-else>--Заказов нет--</span>
                                                </option>
                                            </template>

                                            <option :key="order.id" :value="order"
                                                    v-for="order in orders">
                                                {{order.totalPrice}} USD {{order.created_at}}
                                            </option>
                                        </b-form-select>

                                        <span class="invalid-feedback" role="alert" v-if="$v.order.$error">
                                            <strong>Необходимо выбрать заказ</strong>
                                        </span>

                                        <span class="invalid-feedback" role="alert" v-if="errors.order">
                                             <strong v-for="message in errors.order">{{message}}</strong>
                                        </span>
                                    </b-form-group>
                                </div>

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
                                        <b-input-group :append="exchange.to_currency.isoName">
                                            <b-form-input disabled id="convertedAmount"
                                                          v-model="convertedAmount"></b-form-input>

                                        </b-input-group>
                                    </b-form-group>
                                </div>
                            </div>

                            <div class="row">
                                <div class="form-group col-12">
                                    <label for="comment">Комментарий</label>
                                    <input class="form-control" id="comment" type="text" v-model="comment"
                                    placeholder="Добавьте произвольный комментарий (необязательно)">
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
    import {required, decimal} from 'vuelidate/lib/validators';

    const validateAmount = (value, vm) => {
        if (vm.requiredAmount) {
            let compareTo = vm.needConverting ? vm.convertedAmount : vm.amount;
            console.log(compareTo, vm.requiredAmount);
            return vm.requiredAmount === compareTo;
        } else
            return vm.amount > 0;
    };

    const validateOrder = (value, vm) => {
        if (vm.isOrderPayment)
            return vm.order && vm.order.id;
        else
            return true;
    };

    export default {
        name: "IncomingPaymentEditor",
        mounted() {
            this.currency = this.currencies[0];
            this.paymentType = 'in'
        },
        props: {
            accountTo: {
                type: Object,
                required: false
            },
            currencies: {
                type: Array,
                required: true
            }
        },
        data() {
            return {
                client: null,
                amount: 0,
                currency: null,
                paymentItem: null,
                paymentType: null,
                comment:null,
                needConverting: false,
                orders: [],
                order: null,
                requiredAmount: null,
                exchange: {
                    coefficient: null,
                    to_currency: {
                        isoName: null
                    }
                },
                errors: {
                    client: null,
                    amount: null,
                    currency: null,
                    paymentItem: null,
                    order: null,
                    exchange: null
                },
                paymentItems: []
            }
        },
        watch: {
            paymentType() {
                this.$bvModal.show('busyModal');
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
                        this.$bvModal.hide('busyModal')
                    }
                )
            },
            currency() {
                this.needConverting = this.currency.id !== this.accountTo.currency.id;
                if (this.needConverting)
                    this.convert()
            },
            paymentItem() {
                this.order = null;
                this.getOrders();
            },
            order() {
                if (this.order)
                    this.requiredAmount = this.order.totalPrice;
                else
                    this.requiredAmount = null;
            }
        },
        computed: {
            convertedAmount() {
                return this.amount * this.exchange.coefficient;
            },
            isOrderPayment() {
                return this.paymentItem && this.paymentItem.title.toLowerCase() === 'оплата заказа'
            }
        },
        methods: {
            clientSelected(client) {
                this.client = client;
                this.getOrders();
            },
            async getOrders() {
                if (!this.client)
                    this.orders = [];
                else if (this.isOrderPayment) {
                    let action = '/orders/' + this.client.id + '/unpaid';
                    const response = await axios.get(action);
                    this.orders = response.data;//TODO
                }
            },
            async convert() {
                this.$bvModal.show('busyModal');
                let action = `exchange-history/rate/${this.currency.id}/${this.accountTo.currency.id}`;
                try {
                    const result = await axios.get(action);
                    this.exchange = result.data;
                } catch (e) {
                    this.$root.showErrorMsg('Ошибка загрузки',
                        'Не удалось загрузить курс валют. Убедитесь, что курс для данной валюты создан.')
                }

                this.$nextTick(
                    () => {
                        this.$bvModal.hide('busyModal');
                    }
                )
            },
            async submitForm() {
                this.$bvModal.show('busyModal');
                if (this.$v.$invalid)
                    this.$v.$touch();
                else {
                    let data = {
                        payerId: this.client.id,
                        paymentItemId: this.paymentItem.id,
                        currencyId: this.currency.id,
                        accountTo: this.accountTo.id,
                        amount: this.amount,
                        exchangeId: this.exchange.id,
                        orderId: this.order ? this.order.id : null,
                        comment: this.comment
                    };

                    try {
                        const result = await axios.post('/incoming-payments', data)
                        window.location.href = '/payments'
                    } catch (e) {
                        if (e.response.status === 422) {
                            this.errors.client = e.response.data.errors.payerId;
                            this.errors.order = e.response.data.errors.orderId;
                            this.errors.amount = e.response.data.errors.amount;
                            this.errors.currency = e.response.data.errors.currencyId;
                            this.errors.paymentItem = e.response.data.errors.paymentItemId;
                            this.errors.exchange = e.response.data.errors.exchangeId;
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
            order: {
                validateOrder
            }
        }
    }
</script>

<style scoped>

</style>
