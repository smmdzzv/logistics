<template>
    <div class="container">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Профиль пользователя</h5>
            </div>
                <div class="card-body">
                    <div class="row mt-2">
                        <h4 class="card-title col-sm-6">{{user.name}} {{user.code}}</h4>
                        <a class="col-sm-6 text-right" :href="editUserUrl">Редактировать</a>
                    </div>
                    <h6 class="text-muted card-subtitle mb-5">{{user.email}}  {{user.phone}}</h6>
                    <div class="jumbotron">
                        <orders-table :action="action"></orders-table>
                    </div>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        name: "Profile",
        props:{
            user:{
                type:Object,
                required:true
            }
        },
        computed:{
            action:function () {
                return '/user/' + this.user.id + '/orders'
            },
            editUserUrl: function () {
                return '/user/' + this.user.id + '/edit'
            }
        }
    }
</script>

<style scoped>

</style>
