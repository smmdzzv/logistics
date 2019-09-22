<template>
    <div id="order">
        <div class="row justify-content-center mb-4">
            <div class="col-11">
                <label class="col-12" for="user">Клиент</label>
                <search-user-dropdown class="col-12"
                                      :selected="onUserSelected"
                                      url="/concrete/client/filter?userInfo="
                                      placeholder="Введите ФИО или код клиента"
                                      id="user"/>

                <b-popover
                    :show.sync="clientError"
                    content="Необходимо выбрать клиента. Начните вводить ФИО или код клиента"
                    placement="bottom"
                    target="user"
                    triggers="null"
                    variant="danger"/>
            </div>

        </div>



        <order-items-box :tariffs="tariffs" :user="user"
                         v-on:onStoredItemsChange="onStoredItemsChange"></order-items-box>
        <div class="container">
            <div class="row">
                <div class="col-md-12 text-right pt-4" v-if="storedItems.length > 0">
                    <button @click.stop.prevent.capture="submitData()" class="btn btn-primary">Оформить заказ</button>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        name: "OrderEditor",
        props: {
            user: null,
            tariffs: Array
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
                    this.$bvModal.show('busyModal');

                    try {
                        const response = await axios.post('/orders', {
                            storedItems: this.storedItems,
                            clientId: this.client.id
                        });

                        window.location.href = '/orders/' + response.data.id;
                    } catch (e) {
                        //TODO
                    }
                } else if (!this.client)
                    this.clientError = true;
                this.$bvModal.hide('busyModal');
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
