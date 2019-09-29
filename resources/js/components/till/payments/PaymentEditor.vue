<template>
    <div class="container">
        <form>
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
                                    <b-form-select v-model="paymentType">
                                        <template v-slot:first>
                                            <option :value="null" disabled>-- Выберите тип операции --</option>
                                        </template>
                                        <option value="in">ПРИХОД</option>
                                        <option value="out">РАСХОД</option>
                                    </b-form-select>
                                </div>
                            </div>

                            <div class="row">
                                <div class="form-group col-md-6">
                                    <label for="client">Плательщик</label>
                                    <search-user-dropdown :selected="clientSelected"></search-user-dropdown>
                                    <input class="form-control" id="client" type="hidden">
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
                                <div class="form-group col-6 col-md-3">
                                    <label for="amount">Сумма</label>
                                    <input class="form-control" id="amount" type="number" v-model.number="amount">


                                    <span class="invalid-feedback" role="alert" v-if="$v.amount.$error">
                                        <strong>Необходимо ввести сумму платежа</strong>
                                    </span>

                                    <span class="invalid-feedback" role="alert" v-if="errors.amount">
                                         <strong v-for="message in errors.amount">{{message}}</strong>
                                    </span>
                                </div>

                                <div class="form-group col-6 col-md-3">
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

                                <div class="form-group col-md-6">
                                    <label>Статья</label>
                                    <b-form-select :class="{'is-invalid':$v.paymentItem.$error  || errors.paymentItem}"
                                                   v-model="paymentItem">
                                        <template v-slot:first>
                                            <option :value="null" disabled>-- Выберите статью прихода --</option>
                                        </template>
                                        <option :key="paymentItem.id" :value="paymentItem"
                                                v-for="paymentItem in paymentItems">
                                            {{paymentItem.title.toUpperCase()}}
                                        </option>
                                    </b-form-select>

                                    <span class="invalid-feedback" role="alert" v-if="$v.paymentItem.$error">
                                <strong>Необходимо выбрать статью прихода</strong>
                            </span>

                                    <span class="invalid-feedback" role="alert" v-if="errors.paymentItem">
                                         <strong v-for="message in errors.paymentItem">{{message}}</strong>
                                    </span>
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

    export default {
        name: "PaymentEditor",
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
                needConverting: false,
                errors: {
                    client: null,
                    amount: null,
                    currency: null,
                    paymentItem: null
                },
                paymentItems: []
            }
        },
        watch: {
            paymentType() {
                this.$bvModal.show('busyModal');
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
                this.needConverting = this.currency.id !== this.accountTo.currency.id
            }
        },
        methods: {
            clientSelected(client) {
                this.client = client;
            }
        },
        components: {
            'SearchUserDropdown': require('../../users/SearchUserDropdown.vue').default,
        },
        validations: {
            client: {
                required
            },
            currency: {
                required
            },
            amount: {
                required,
                decimal
            },
            paymentItem: {
                required
            }
        }
    }
</script>

<style scoped>

</style>
