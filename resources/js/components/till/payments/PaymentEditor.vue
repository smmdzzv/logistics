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
                        <b-form-select id="status" class="form-control">
                            <option :value="null" disabled>-- Выберите статус операции --</option>
                            <option value="pending">ЗАЯВКА</option>
                            <option value="completed">ПРОВЕДЕННАЯ</option>
                        </b-form-select>
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
                        <search-user-dropdown v-if="isPayerIndividual" style="width:50%" :selected="onPayerSelected"></search-user-dropdown>
                        <b-form-select v-else>
                            <option :value="null" disabled>-- Выберите филиал --</option>
                            <option v-for="branch in branches" :value="branch">{{branch.name}}</option>
                        </b-form-select>
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
                        <search-user-dropdown  v-if="isPayeeIndividual" style="width:51%" :selected="onPayeeSelected"></search-user-dropdown>
                        <b-form-select v-else>
                            <option :value="null" disabled>-- Выберите филиал --</option>
                            <option v-for="branch in branches" :value="branch">{{branch.name}}</option>
                        </b-form-select>
                    </b-input-group>
                </div>

                <div class="row">
                    <div class="col-md-4 form-group">
                        <label for="paymentItem">Статья</label>
                        <b-form-select id="paymentItem" class="form-control">
                            <option :value="null" disabled>-- Выберите статью --</option>
                            <option v-for="paymentItem in paymentItems" :value="paymentItem">{{paymentItem.title}}</option>
                        </b-form-select>
                    </div>

                    <div class="col-md-4 form-group">
                        <label for="billAmount">Сумма</label>
                        <b-form-input id="amount" class="form-control" type="number"></b-form-input>
                    </div>

                    <div class="col-md-4 form-group">
                        <label for="billCurrency">Валюта</label>
                        <b-form-select id="billCurrency">
                            <option :value="null" disabled>-- Выберите валюту --</option>
                            <option v-for="currency in currencies" :value="currency">
                                {{currency.name.charAt(0).toUpperCase() + currency.name.slice(1)}}
                                {{currency.isoName}}
                            </option>
                        </b-form-select>
                    </div>
                </div>

                <div class="row my-3">
                    <h4 class="m-auto">Сумма к оплате</h4>
                </div>

                <div class="row">
                    <div class="col-md-4 form-group">
                        <label for="currentRate">Обменный курс</label>
                        <b-form-input id="currentRate" class="form-control" type="number" disabled></b-form-input>
                    </div>

                    <div class="col-md-4 form-group">
                        <label for="amount">Сумма</label>
                        <b-form-input id="amount" class="form-control" type="number" disabled></b-form-input>
                    </div>

                    <div class="col-md-4 form-group">
                        <label for="billCurrency">Валюта оплаты</label>
                        <b-form-select id="billCurrency">
                            <option :value="null" disabled>-- Выберите валюту --</option>
                            <option v-for="currency in currencies" :value="currency">
                                {{currency.name.charAt(0).toUpperCase() + currency.name.slice(1)}}
                                {{currency.isoName}}
                            </option>
                        </b-form-select>
                    </div>
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
            paymentItems:{
                type: Array,
                required: true
            }
        },
        data() {
            return {
                isPayerIndividual: false,
                isPayeeIndividual: false,
            }
        },
        methods:{
            onPayerSelected(){

            },
            onPayeeSelected(){

            }
        },
        components: {
            'SearchUserDropdown': require('../../users/SearchUserDropdown.vue').default
        }
    }
</script>

<style scoped>

</style>
