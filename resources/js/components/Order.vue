<template>
    <div id="order">
        <b-modal
            no-close-on-esc
            no-close-on-backdrop
            hide-footer
            hide-header
            centered
            content-class="bg-transparent border-0"
            id="busyModal">
            <div class="d-block text-center">
                <b-spinner variant="light" label="Busy" style="width: 6rem; height: 6rem"/>
            </div>
        </b-modal>

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
<!--                <div class="col-md-12 text-right pt-4" v-if="storedItems.length > 0">-->
                <div class="col-md-12 text-right pt-4">
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
            submitData() {
                return  this.$bvModal.show('busyModal');
                if (!this.client)
                    return this.clientError = true;
                if (this.storedItems.length > 0) {
                    axios.post('/order/store', {
                        storedItems: this.storedItems,
                        clientId: this.client.id
                    })
                }
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
