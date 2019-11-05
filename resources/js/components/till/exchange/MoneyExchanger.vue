<template>
    <div>
        <div class="row">
            <div class="form-group col-md-4">
                <label>Конвертировать из</label>
                <b-select v-model="from">
                    <option :value="null">-- Выберите валюту --</option>
                    <option :key="currencies.id" :value="currency" v-for="currency in currencies">{{currency.name}}
                        ({{currency.isoName}})
                    </option>
                </b-select>
            </div>

            <div class="form-group col-md-4">
                <label>Конвертировать в</label>
                <b-select v-model="to">
                    <option :value="null">-- Выберите валюту --</option>
                    <option :key="currencies.id" :value="currency" v-for="currency in currencies">{{currency.name}}
                        ({{currency.isoName}})
                    </option>
                </b-select>
            </div>

            <div class="form-group col-md-4">
                <label for="amount">Сумма</label>
                <input class="form-control" id="amount" step="0.01" type="number" v-model="amount">
                <b-tooltip target="amount" trigger="hover">
                    Сумма вносимая клиентом в конвертируемой валюте
                </b-tooltip>
            </div>
        </div>

        <div class="row">
            <div class="form-group col-md-3">
                <label for="rate">Курс</label>
                <input :value="rate.coefficient" class="form-control" disabled id="rate" type="number">
            </div>
            <div class="form-group col-md-3">
                <label for="converted">Сумма к выдаче</label>
                <input :value="converted" class="form-control" disabled id="converted" type="number">
            </div>
            <div class="form-group col-md-6">
                <label for="comment">Комментарий</label>
                <input v-model="comment" class="form-control" id="comment" type="text">
            </div>
        </div>

        <div class="row justify-content-center mt-4">
            <button @click="submit" class="btn btn-primary">Обменять</button>
        </div>
    </div>
</template>

<script>
    export default {
        name: "MoneyExchanger",
        props: {
            currencies: {
                type: Array,
                required: true
            }
        },
        data() {
            return {
                from: null,
                to: null,
                rate: {
                    coefficient: 0
                },
                amount: 0,
                converted: 0,
                comment: null
            }
        },
        watch: {
            from() {
                this.getExchangeRate();
            },
            to() {
                this.getExchangeRate();
            },
            rate() {
                this.convert();
            },
            amount() {
                this.convert();
            }
        },
        methods: {
            async getExchangeRate() {
                if (!this.to || !this.from)
                    return;

                tShowSpinner();
                try {
                    let action = `/exchange-history/rate/${this.from.id}/${this.to.id}`;
                    const response = await axios.get(action);
                    this.rate = response.data;
                } catch (e) {
                    this.$root.showErrorMsg(
                        'Ошибка загрузки',
                        'Не удалось загрузить курс для выбранных валют'
                    )
                }

                tHideSpinner();
            },
            convert() {
                this.converted = (this.rate.coefficient * this.amount).toFixed(2);
            },
            async submit() {
                if (!this.to || !this.from || !this.amount)
                    return;

                tShowSpinner();

                try {
                    let data = {
                        from: this.from.id,
                        to: this.to.id,
                        amount: this.amount,
                        comment: this.comment
                    };

                    const response = await axios.post('/exchange-money', data);
                    window.location = '/payments';
                } catch (e) {
                    this.$root.showErrorMsg(
                        'Ошибка сохранения',
                        'Не удалось провести операцию. Повторите попытку после перезагрузки страницы')
                }

                tHideSpinner();
            }
        }
    }
</script>
