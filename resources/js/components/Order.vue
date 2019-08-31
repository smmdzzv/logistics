<template>
    <div>
        <search-user-dropdown v-on:userSelected="onUserSelected"></search-user-dropdown>
        <order-items-box></order-items-box>
    </div>
</template>

<script>
    export default {
        name: "Order",
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
