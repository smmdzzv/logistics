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
                                        <option value="out">РАСХОД</option>
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
                                <div class="form-group col-md-12">
                                    <label>Счет списания</label>
                                    <!--                                    <input class="form-control" disabled id="accountFrom" type="text"-->
                                    <!--                                           v-model="accountFrom.description">-->
                                    <b-select v-model="accountFrom">
                                        <option :key="account.id" :value="account" v-for="account in accountsFrom">
                                            {{account.description}}
                                        </option>
                                    </b-select>
                                </div>
                            </div>

                            <div class="row">
                                <div class="form-group col-md-6">
                                    <label>Статья</label>
                                    <b-form-select :class="{'is-invalid':$v.paymentItem.$error  || errors.paymentItem}"
                                                   v-model="paymentItem">
                                        <template v-slot:first>
                                            <option :value="null" disabled>
                                                -- Выберите статью расхода --
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
                                                   disabled v-model="currency">
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
    import {required, decimal} from 'vuelidate/lib/validators';

    const validateAmount = (value, vm) => {
        return vm.amount > 0;
    };

    export default {
        name: "OutgoingPaymentEditor",
        mounted() {
            this.paymentType = 'out';

            if (this.disable)
                this.disableForm();

            if (this.payment)
                this.setInitialData();

            if (this.accountsFrom && this.accountsFrom.length > 0)
                this.accountFrom = this.accountsFrom[0]
        },
        props: {
            accountsFrom: {
                type: Array,
                required: false
            },
            currencies: {
                type: Array,
            },
            paymentItems: {
                type: Array,
            },
            payment: {
                type: Object
            },
            disable: {
                type: Boolean
            }
        },
        data() {
            return {
                amount: 0,
                accountFrom: 0,
                currency: null,
                paymentItem: null,
                paymentType: null,
                comment: null,
                requiredAmount: null,
                status: 'pending',
                errors: {
                    amount: null,
                    paymentItem: null,
                },
            }
        },
        methods: {
            disableForm() {
                $("input").prop("disabled", true);
                $("select").prop("disabled", true);
                $("#status").prop("disabled", false);
            },
            setInitialData() {
                this.currencies = [this.payment.currency];
                this.paymentItems = [this.payment.paymentItem];
                this.accountsFrom = [this.payment.accountFrom];
                this.paymentType = this.payment.paymentItem.type;

                this.paymentItem = this.payment.paymentItem;
                this.status = this.payment.status;
                this.currency = this.payment.currency;
                this.accountFrom = this.payment.accountFrom;
                this.amount = this.payment.amount;
                this.comment = this.payment.comment;
            },
            async submitForm() {
                this.$bvModal.show('busyModal');
                if (this.$v.$invalid)
                    this.$v.$touch();
                else {
                    let data = {
                        currencyId: this.currency.id,
                        paymentItemId: this.paymentItem.id,
                        amount: this.amount,
                        comment: this.comment,
                        status: this.status,
                        accountFrom: this.accountFrom.id,
                        id: this.payment ? this.payment.id : null
                    };

                    try {
                        if (this.payment)
                            await axios.patch('/outgoing-payments/' + this.payment.id, data);
                        else
                            await axios.post('/outgoing-payments', data);
                        window.location.href = '/payments'
                    } catch (e) {
                        console.log(e);
                        if (e.response.status === 422) {
                            this.errors.amount = e.response.data.errors.amount;
                            this.errors.paymentItem = e.response.data.errors.paymentItemId;
                        } else {
                            this.$root.showErrorMsg('Ошибка сохранения',
                                'Не удалось провести платеж. Обновите странице и повторите попытку.' + e.response.data.message)
                        }
                    }
                }
                this.$nextTick(() => {
                        this.$bvModal.hide('busyModal')
                    }
                );

            },
        },
        watch: {
            accountFrom() {
                this.currency = this.accountFrom.currency;
            }
        },
        components: {
            'SearchUserDropdown': require('../../../users/SearchUserDropdown.vue').default,
        },
        validations: {
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
        }
    }
</script>

<style scoped>

</style>
