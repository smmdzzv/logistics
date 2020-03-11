<template>
    <div id="order">
        <div class="row justify-content-center px-3">
            <div class="form-group col-12">
                <label class="col-form-label text-md-right" for="user">Клиент</label>
                <input class="form-control" id="user" placeholder="Введите код клиента" v-model.lazy="clientCode"
                       autofocus>
                <!--                <search-user-dropdown :selected="onUserSelected"-->
                <!--                                      :preselectedUser="client"-->
                <!--                                      class="col-12"-->
                <!--                                      id="user"-->
                <!--                                      ref="clientDropdown"-->
                <!--                                      placeholder="Введите ФИО или код клиента"-->
                <!--                                      url="/concrete/client/filter?userInfo="-->
                <!--                                      autofocus/>-->

                <b-popover
                    :show.sync="clientError"
                    content="Введите код клиента"
                    placement="bottom"
                    target="user"
                    triggers="null"
                    variant="danger"/>
            </div>
        </div>
        <div class="row px-3" v-if="!client">
            <div class="form-group col-4">
                <label class="col-form-label text-md-right" for="clientName">ФИО</label>
                <input class="form-control client-input-form" id="clientName" placeholder="Необязательно"
                       v-model="clientName">
            </div>
            <div class="form-group col-4">
                <label class="col-form-label text-md-right" for="clientPhone">Телефон</label>
                <input class="form-control client-input-form" id="clientPhone" placeholder="Необязательно"
                       v-model="clientPhone">
            </div>
            <div class="form-group col-4">
                <label class="col-form-label text-md-right" for="clientEmail">E-mail</label>
                <input class="form-control client-input-form" id="clientEmail" placeholder="Необязательно"
                       v-model="clientEmail">
            </div>
        </div>
        <hr>
        <order-items-box :tariffs="tariffs"
                         :order="order"
                         :user="user"
                         v-on:onStoredItemsChange="onStoredItemsChange"/>


        <div class="row">
            <div class="col-md-12 text-right pt-4" v-if="storedItems.length > 0">
                <button @click.stop.prevent.capture="submitData()" class="btn btn-primary col-12">Оформить заказ
                </button>
            </div>
        </div>

    </div>
</template>

<script>
    export default {
        name: "OrderEditor",
        mounted() {
            if (this.order) {
                this.clientCode = this.order.owner.code;
                // this.$refs.clientDropdown.userInfo = this.clientCode;
                // this.$refs.clientDropdown.selectedUserDisplayInfo = this.clientCode;
            }
        },
        props: {
            user: null,
            tariffs: Array,
            order: Object
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
            // onUserSelected(user) {
            //     this.client = user;
            //     this.clientCode = this.client ? this.client.code : this.$refs.clientDropdown.userInfo;
            // },
            async submitData() {
                if (!this.clientCode) {
                    this.clientError = true;
                    return;
                }

                if (this.storedItems.length > 0) {
                    // this.$bvModal.show('busyModal');
                    tShowSpinner();
                    try {
                        let response = null;
                        if (this.order) {
                            response = await axios.patch('/orders/' + this.order.id, {
                                storedItemInfos: this.storedItems,
                                clientCode: this.clientCode
                            });
                        } else {
                            response = await axios.post('/orders', {
                                storedItemInfos: this.storedItems,
                                clientCode: this.clientCode,
                                clientName: this.clientName,
                                clientPhone: this.clientPhone,
                                clientEmail: this.clientEmail
                            });
                        }

                        window.location.href = '/orders/create';
                    } catch (e) {
                        tHideSpinner();
                        this.$root.showErrorMsg(
                            'Ошибка сохранения',
                            'Не удалось сохранить заказ. Попробуйте принять заказ позже'
                        );
                        console.log(e);
                    }
                }

                // this.$bvModal.hide('busyModal');

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
