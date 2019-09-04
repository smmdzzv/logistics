<template>
    <div>
        <search-user-dropdown v-on:userSelected="onUserSelected"></search-user-dropdown>
        <order-items-box :user="user" :tariffs = "tariffs"></order-items-box>
        <div class="container">
            <div class="row">
                <div class="col-md-12 text-right pt-4">
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
                client: null
            }
        },
        methods:{
            onUserSelected(user){
                this.client = user;
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
