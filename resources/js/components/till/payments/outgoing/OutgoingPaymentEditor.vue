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
                                    <b-form-select v-model="paymentType" disabled>
                                        <option value="out">РАСХОД</option>
                                    </b-form-select>
                                </div>
                            </div>

                            <div class="row">
                                <div class="form-group col-md-12">
                                    <label for="accountFrom">Счет списания</label>
                                    <input class="form-control" disabled id="accountFrom" type="text"
                                           v-model="accountFrom.description">
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
                                                   v-model="currency" disabled>
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
            return vm.amount > 0;
    };

    export default {
        name: "OutgoingPaymentEditor",
        mounted() {
            this.currency = this.accountFrom.currency;
            this.paymentType = 'out'
        },
        props: {
            accountFrom: {
                type: Object,
                required: false
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
                amount: 0,
                currency: null,
                paymentItem: null,
                paymentType: null,
                comment:null,
                requiredAmount: null,
                errors: {
                    amount: null,
                    paymentItem: null,
                },
            }
        },
        methods: {
            async submitForm() {
                this.$bvModal.show('busyModal');
                if (this.$v.$invalid)
                    this.$v.$touch();
                else {
                    let data = {
                        paymentItemId: this.paymentItem.id,
                        amount: this.amount,
                        comment: this.comment
                    };

                    try {
                        const result = await axios.post('/outgoing-payments', data)
                        window.location.href = '/payments'
                    } catch (e) {
                        console.log(e)
                        if (e.response.status === 422) {
                            this.errors.amount = e.response.data.errors.amount;
                            this.errors.paymentItem = e.response.data.errors.paymentItemId;
                        } else {
                            this.$root.showErrorMsg('Ошибка сохранения',
                                'Не удалось провести платеж. Обновите странице и повторите попытку')
                        }
                    }
                }
                this.$nextTick(()=>{
                    this.$bvModal.hide('busyModal')
                    }
                );

            },
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
