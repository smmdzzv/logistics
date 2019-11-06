<template>

    <form @submit.prevent="submit">

        <div class="form-group">
            <label for="client">Клиент</label>
            <search-user-dropdown :preselectedUser="client"
                                  :selected="clientSelected"
                                  placeholder="Введите ФИО или код клиента"
                                  url="/concrete/client/filter?userInfo="></search-user-dropdown>
            <input class="is-invalid form-control" id="client" type="hidden">
            <span class="invalid-feedback"
                  role="alert"
                  v-if="$v.client.$error || errors.client">
                            <strong>Необходимо выбрать клиента.</strong>
                            <strong v-for="message in errors.client">{{message}}.</strong>
            </span>
        </div>

        <div class="row align-items-baseline">
            <div class="form-group col-md-4">
                <label for="from">Дата начала</label>
                <input :class="{'is-invalid':$v.from.$error  || errors.from}"
                       class="form-control"
                       id="from"
                       type="date">

                <span class="invalid-feedback"
                      role="alert"
                      v-if="$v.from.$error || errors.from">
                            <strong>Необходимо выбрать клиента.</strong>
                            <strong v-for="message in errors.from">{{message}}.</strong>
            </span>
            </div>
            <div class="form-group col-md-4">
                <label for="to">Дата конца</label>
                <input :class="{'is-invalid':$v.to.$error  || errors.to}"
                       class="form-control"
                       id="to"
                       type="date">

                <span class="invalid-feedback"
                      role="alert"
                      v-if="$v.to.$error || errors.to">
                            <strong>Необходимо выбрать клиента.</strong>
                            <strong v-for="message in errors.to">{{message}}.</strong>
            </span>
            </div>
            <div class="form-group col-md-4">
                <label for="maxDebt">Макс. допустимый долг</label>
                <input :class="{'is-invalid':$v.maxDebt.$error  || errors.maxDebt}"
                       class="form-control"
                       id="maxDebt"
                       placeholder="в долларах (USD)"
                       step="0.01"
                       type="number">

                <span class="invalid-feedback"
                      role="alert"
                      v-if="$v.maxDebt.$error || errors.maxDebt">
                            <strong>Необходимо выбрать клиента.</strong>
                            <strong v-for="message in errors.maxDebt">{{message}}.</strong>
                </span>
            </div>
        </div>

        <div class="row">
            <button class="mx-auto mt-4 btn btn-primary" type="submit">Сохранить</button>
        </div>

    </form>

</template>


<script>
    import {required} from 'vuelidate/lib/validators';

    export default {
        name: "TrustedUserEditor",
        components: {
            'SearchUserDropdown': require('../SearchUserDropdown.vue').default,
        },
        data() {
            return {
                client: null,
                from: null,
                to: null,
                maxDebt: null,
                errors: {
                    client: null,
                    from: null,
                    to: null,
                    maxDebt: null,
                }
            }
        },
        methods: {
            clientSelected(client) {
                this.client = client;
            },
            async submit() {
                if (this.$v.$invalid) {
                    this.$v.$touch();
                    return;
                }

                try {
                    let data = {
                        client: this.client,
                        from: this.from,
                        to: this.to,
                        maxDebt: this.maxDebt
                    };

                    const response = await axios.post('/trusted-user', data);

                } catch (e) {
                    if (e.response.status === 422) {
                        this.errors.client = e.response.data.errors.client;
                        this.errors.from = e.response.data.errors.from;
                        this.errors.to = e.response.data.errors.to;
                        this.errors.maxDebt = e.response.data.errors.maxDebt;
                    } else {
                        this.$root.showErrorMsg('Ошибка соранения',
                            'Не удалось добавить пользователя в список доверенных. ' +
                            'Обновите странице и повторите попытку')
                    }
                }

            }
        },
        validations: {
            client: {
                required
            },
            from: {
                required
            },
            to: {
                required
            },
            maxDebt: {
                required
            },
        }
    }
</script>

<style scoped>

</style>
