<template>
    <div class="container">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Профиль пользователя</h5>
            </div>
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <h4 class="card-title col-md-8">{{user.name}} {{user.code}}</h4>
                        <a class="col-md-6 text-right" href="#">Редактировать</a>
                    </div>
                    <h6 class="text-muted card-subtitle mb-2">{{user.email}}  {{user.phone}}</h6>
                    <div class="jumbotron">
                        <h1>Заказы</h1>
                        <orders-table></orders-table>
                    </div>
                    <p class="card-text">Nullam id dolor id nibh ultricies vehicula ut id elit. Cras justo odio, dapibus ac facilisis in, egestas eget quam. Donec id elit non mi porta gravida at eget metus.</p><a href="#" class="card-link">Link</a><a href="#" class="card-link">Link</a></div>
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
            },
            orders:{
                type:Array,
                required:true
            }
        }
    }
</script>

<style scoped>

</style>
