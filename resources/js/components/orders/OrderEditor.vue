<template>
    <div id="order">
        <div class="row justify-content-center px-3">
            <div class="form-group col-3">
                <label class="col-form-label text-md-right" for="user">Клиент</label>
                <input class="form-control form-control-sm" id="user" placeholder="Введите код клиента"
                       v-model.lazy="clientCode"
                       autofocus>

                <b-popover
                    :show.sync="clientError"
                    content="Введите код клиента"
                    placement="bottom"
                    target="user"
                    triggers="null"
                    variant="danger"/>
            </div>

            <template v-if="!client">
                <div class="form-group col-3">
                    <label class="col-form-label text-md-right" for="clientName">ФИО</label>
                    <input class="form-control form-control-sm client-input-form" id="clientName"
                           placeholder="Необязательно"
                           v-model="clientName">
                </div>
                <div class="form-group col-3">
                    <label class="col-form-label text-md-right" for="clientPhone">Телефон</label>
                    <input class="form-control form-control-sm client-input-form" id="clientPhone"
                           placeholder="Необязательно"
                           v-model="clientPhone">
                </div>
                <div class="form-group col-3">
                    <label class="col-form-label text-md-right" for="clientEmail">E-mail</label>
                    <input class="form-control form-control-sm client-input-form" id="clientEmail"
                           placeholder="Необязательно"
                           v-model="clientEmail">
                </div>
            </template>
        </div>

        <hr>
        <order-items-box :tariffs="tariffs"
                         :order="order"
                         :user="user"
                         :branches="branches"
                         v-on:onStoredItemsChange="onStoredItemsChange"/>

        <div class="row">
            <div class="col-md-12 text-right pt-4" v-if="storedItems.length > 0 || order && order.id">
                <button @click.stop.prevent.capture="submitData()" class="btn btn-primary col-12">
                    <span v-if="order && order.id">Обновить заказ</span>
                    <span v-else>Оформить заказ</span>
                </button>
            </div>
        </div>

    </div>
</template>

<script>
    import {hideBusySpinner, showBusySpinner} from "../../tools";

    export default {
        name: "OrderEditor",
        mounted() {
            if (this.order) {
                this.clientCode = this.order.owner.code;
            }
        },
        props: {
            user: null,
            tariffs: Array,
            order: Object,
            branches: Array
        },
        data() {
            return {
                client: null,
                clientCode: null,
                clientName: null,
                clientPhone: null,
                clientEmail: null,
                storedItems: [],
                clientError: false
            }
        },
        methods: {
            async submitData() {
                if (!this.clientCode) {
                    this.clientError = true;
                    return;
                }

                if (this.storedItems.length > 0 || this.order && this.order.id) {
                    // this.$bvModal.show('busyModal');
                    showBusySpinner();
                    let storedItemInfos = this.storedItems.map(i => {
                        i.branch_id = i.branch.id;
                        i.item_id = i.item.id;
                        i.tariff_id = i.tariff.id;
                        i.customs_code_id = i.customsCode.id;
                        return i;
                    });
                    let customPrices = this.storedItems.map(i => i.customPrice ? i.customPrice : null)
                    try {
                        let response = null;
                        if (this.order) {
                            response = await axios.patch('/orders/' + this.order.id, {
                                branch_id: storedItemInfos[0].branch_id,
                                storedItemInfos: storedItemInfos,
                                customPrices: customPrices,
                                clientCode: this.clientCode
                            });
                        } else {
                            response = await axios.post('/orders', {
                                branch_id: storedItemInfos[0].branch_id,
                                storedItemInfos: storedItemInfos,
                                customPrices: customPrices,
                                clientCode: this.clientCode,
                                clientName: this.clientName,
                                clientPhone: this.clientPhone,
                                clientEmail: this.clientEmail
                            });
                        }
                        window.open('/print/order-labels/' + response.data.id, "_blank");
                        window.location.href = '/orders/' + response.data.id;
                    } catch (e) {
                        hideBusySpinner();
                        this.$root.showErrorMsg(
                            'Ошибка сохранения',
                            'Не удалось сохранить заказ. Попробуйте принять заказ позже'
                        );
                        console.log(e);
                    }
                }
            },
            onStoredItemsChange(items) {
                if (items)
                    this.storedItems = items;
            }
        },
        watch: {
            clientCode() {
                if (this.clientCode)
                    axios.get(`/concrete/client/findByCode?code=${this.clientCode}`)
                        .then(response => {
                            this.client = response;

                            if ($('.client-input-form:focus').length > 0)
                                $('#shop').focus();
                        })
                        .catch(e => {
                            this.client = null;
                        })
            }
        },
        components: {
            'SearchUserDropdown': require('../users/SearchUserDropdown.vue').default,
            'OrderItemsBox': require('./OrderItemsBox').default
        }
    }
</script>

<style scoped>

</style>
