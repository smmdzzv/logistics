<template>
    <div id="order">
        <div class="row justify-content-center mb-4">
            <div class="col-12">
                <label class="col-12" for="user">Клиент</label>
                <search-user-dropdown :selected="onUserSelected"
                                      :preselectedUser="client"
                                      class="col-12"
                                      id="user"
                                      placeholder="Введите ФИО или код клиента"
                                      url="/concrete/client/filter?userInfo="/>

                <b-popover
                    :show.sync="clientError"
                    content="Необходимо выбрать клиента. Начните вводить ФИО или код клиента"
                    placement="bottom"
                    target="user"
                    triggers="null"
                    variant="danger"/>
            </div>
        </div>

        <order-items-box :tariffs="tariffs"
                         :order="order"
                         :user="user"
                         :shops="shops"
                         v-on:onStoredItemsChange="onStoredItemsChange"/>


        <div class="row">
            <div class="col-md-12 text-right pt-4" v-if="storedItems.length > 0">
                <button @click.stop.prevent.capture="submitData()" class="btn btn-primary col-12">Оформить заказ</button>
            </div>
        </div>

    </div>
</template>

<script>
    export default {
        name: "OrderEditor",
        mounted(){
            if(this.order){
                this.onUserSelected(this.order.owner);
            }
        },
        props: {
            user: null,
            tariffs: Array,
            shops: Array,
            order: Object
        },
        data() {
            return {
                client: null,
                storedItems: [],
                clientError: false
            }
        },
        methods: {
            onUserSelected(user) {
                this.client = user;
                this.clientError = false;
            },
            async submitData() {
                if (this.client && this.storedItems.length > 0) {
                    // this.$bvModal.show('busyModal');
                    tShowSpinner();
                    try {
                        const response = await axios.post('/orders', {
                            storedItemInfos: this.storedItems,
                            clientId: this.client.id
                        });

                        window.location.href = '/orders/' + response.data.id;
                    } catch (e) {
                        tHideSpinner()
                        this.$root.showErrorMsg(
                            'Ошибка сохранения',
                            'Не удалось сохранить заказ. Попробуйте принять заказ позже'
                        )
                    }
                } else if (!this.client)
                    this.clientError = true;
                // this.$bvModal.hide('busyModal');

            },
            onStoredItemsChange(items) {
                if (items)
                    this.storedItems = items;
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
