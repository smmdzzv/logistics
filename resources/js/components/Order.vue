<template>
    <div>
        <search-user-dropdown v-on:userSelected="onUserSelected"></search-user-dropdown>
        <order-items-box :user="user" :tariffs = "tariffs" v-on:onStoredItemsChange="onStoredItemsChange"></order-items-box>
        <div class="container">
            <div class="row">
                <div class="col-md-12 text-right pt-4" v-if="storedItems.length > 0">
                    <button class="btn btn-primary" @click="submitData()">Оформить заказ</button>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        name: "Order",
        mounted(){
            console.log(this.user)
        },
        props:{
            user: null,
            tariffs:Array
        },
        data(){
            return{
                client: null,
                storedItems:[]
            }
        },
        methods:{
            onUserSelected(user){
                this.client = user;
            },
            submitData(){
                if(this.storedItems.length > 0){
                    axios.post('/order/store', {
                        storedItems: this.storedItems
                    })
                }
            },
            onStoredItemsChange(items){
                if(items)
                    this.storedItems = items;
            }
        },
        components:{
            'SearchUserDropdown': require('./SearchUserDropdown.vue').default,
            'OrderItemsBox': require('./OrderItemsBox').default
        }
    }
</script>

<style scoped>

</style>
