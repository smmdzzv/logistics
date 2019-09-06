<template>
    <div id="order">
        <search-user-dropdown id="user" v-on:userSelected="onUserSelected"></search-user-dropdown>
        <b-popover
            :show.sync="clientError"
            variant="danger"
            target="user"
            placement="bottom"
            content="Необходимо выбрать клиента. Начните вводить ФИО или код клиента"
            triggers="null"/>

        <order-items-box :user="user" :tariffs="tariffs"
                         v-on:onStoredItemsChange="onStoredItemsChange"></order-items-box>
        <div class="container">
            <div class="row">
                <div class="col-md-12 text-right pt-4" v-if="storedItems.length > 0">
                    <button class="btn btn-primary" @click.stop.prevent.capture="submitData()">Оформить заказ</button>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        name: "Order",
        mounted() {
            console.log(this.user)
        },
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
                        const response = await axios.post('/order/store', {
                            storedItems: this.storedItems,
                            clientId: this.client.id
                        });

                        window.location.href = '/order/' + response.data.id;
                    } catch (e) {
                        //TODO
                    }
                }
                else if(!this.client)
                    this.clientError = true;
                this.$bvModal.hide('busyModal');
            },
            onStoredItemsChange(items) {
                if (items)
                    this.storedItems = items;
            }
        },
        components: {
            'SearchUserDropdown': require('./SearchUserDropdown.vue').default,
            'OrderItemsBox': require('./OrderItemsBox').default
        }
    }
</script>

<style scoped>

</style>
