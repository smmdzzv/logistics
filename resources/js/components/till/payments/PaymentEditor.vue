<template>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12 col-lg-10">
                <div class="card shadow">
                    <div class="card-header">
                        Проведение платежа
                    </div>
                    <div class="card-body">
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
                                <b-form-select :class="{'is-invalid':$v.expenditure.$error  || errors.expenditure}"
                                               v-model="expenditure">
                                    <template v-slot:first>
                                        <option :value="null" disabled>-- Выберите статью прихода --</option>
                                    </template>
                                    <option :key="expenditure.id" :value="expenditure" v-for="expenditure in expenditures">
                                        {{expenditure.name.toUpperCase()}}
                                    </option>
                                </b-form-select>

                                <span class="invalid-feedback" role="alert" v-if="$v.expenditure.$error">
                                <strong>Необходимо выбрать статью прихода</strong>
                            </span>

                                <span class="invalid-feedback" role="alert" v-if="errors.expenditure">
                                 <strong v-for="message in errors.expenditure">{{message}}</strong>
                            </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    import {required, decimal} from 'vuelidate/lib/validators';

    export default {
        name: "PaymentEditor",
        mounted() {
            this.currency = this.currencies[0]
        },
        props: {
            accountTo: {
                type: Object,
                required: false
            },
            currencies: {
                type: Array,
                required: true
            },
            expenditures:{
                type: Array,
                required: false,
                default:()=>[]
            }
        },
        data() {
            return {
                client: null,
                amount: 0,
                currency: null,
                expenditure:null,
                errors: {
                    client: null,
                    amount: null,
                    currency: null,
                    expenditure: null
                }
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
            amount:{
                required,
                decimal
            },
            expenditure:{
                required
            }
        }
    }
</script>

<style scoped>

</style>
