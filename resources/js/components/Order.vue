<template>
    <search-user-dropdown v-on:userSelected="onUserSelected"></search-user-dropdown>
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
            'search-user-dropdown': require('./SearchUserDropdown.vue').default
        }
    }
</script>

<style scoped>

</style>
