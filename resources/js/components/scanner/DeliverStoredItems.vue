<template>
    <div class="container-fluid">
        <div class="col-12 mb-3" style="margin-left: -30px">
            <label>Клиент</label>
            <search-user-dropdown
                :isInvalid="client === null"
                :preselectedUser="client"
                :selected="clientSelected"
                autofocus
                placeholder="Введите ФИО или код клиента"
                url="/concrete/client/filter?userInfo=">
            </search-user-dropdown>
        </div>

        <div class="row">
            <button class="btn btn-secondary mr-4 mb-3" @click="createPendingPayment">
                Оформить платежную заявку
            </button>

            <button class="btn btn-primary mb-3" @click="submit">
                Выдать товары клиенту
            </button>

            <div class="input-group col-md-6 mb-3">
                <div class="input-group-prepend">
                    <div class="input-group-text">
                        <input type="checkbox" v-model="isDebtRequested" name="isDebtRequested">
                    </div>
                </div>
                <input type="text" class="form-control"
                       value="Учитывать возможность предоставления долга" disabled>
            </div>
        </div>

        <b-modal id="payment-error" title="Ошибка оплаты" ok-only centered ok-title="Закрыть">
            <p class="my-4">{{errorMessage}}</p>
        </b-modal>
    </div>
</template>

<script>
    import {hideBusySpinner, showBusySpinner} from "../../tools";

    export default {
        name: "DeliverStoredItems",
        props: {
            storedItems: {
                type: Array,
                required: true,
            }
        },
        components: {
            'SearchUserDropdown': require('../users/SearchUserDropdown.vue').default
        },
        data() {
            return {
                errorMessage: '',
                client: null,
                isDebtRequested: false
            }
        },
        methods: {
            clientSelected(client) {
                this.client = client
            },
            async createPendingPayment() {
                if (this.storedItems.length === 0 || !this.client)
                    return;

                showBusySpinner();
                this.$bvModal.hide('payment-error');

                try {
                    let data = {
                        storedItems: this.storedItems.map((item) => {
                            return item.id
                        }),
                        isDebtRequested: this.isDebtRequested
                    };

                    let action = `/client/${this.client.id}/stored-items/pending-payment`;

                    const response = await axios.post(action, data);
                    // window.location = `/payment/${response.data.id}`;

                    let win = window.open(`/payment/${response.data.id}`, '_blank');
                    win.focus();
                } catch (e) {
                    console.log(e)
                    if (e.response && e.response.status === 400 || e.response.status === 422) {
                        this.errorMessage = e.response.data.message;
                        this.$bvModal.show('payment-error');
                    }
                } finally {
                    hideBusySpinner();
                }
            },
            async submit() {
                if (this.storedItems.length === 0 || !this.client)
                    return;

                showBusySpinner();
                this.$bvModal.hide('payment-error');

                let data = {
                    storedItems: this.storedItems.map((item) => {
                        return item.id
                    }),
                    isDebtRequested: this.isDebtRequested
                };

                let action = `/client/${this.client.id}/delivered-stored-items`;

                try {
                    const response = await axios.post(action, data);
                    window.location = `/payment/${response.data}`;
                } catch (e) {
                    if (e.response && e.response.status === 400 || e.response.status === 422) {
                        this.errorMessage = e.response.data.message;
                    } else {
                        this.errorMessage = "Не удалось оформить выдачу товаров. Повтороите попытку после перезагрузки страницы"
                    }
                    this.$bvModal.show('payment-error');
                }
                hideBusySpinner();
            },
        }
    }
</script>

<style scoped>

</style>
