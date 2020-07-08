<template>
    <div class="container-fluid">
        <div class="row">
            <button class="btn btn-primary">
                Загрузить на рейс
            </button>
        </div>
    </div>
</template>

<script>
    export default {
        name: "LoadCar",
        props:{
            storedItems:{
                type:Array,
                required:true
            }
        }
    }
</script>

<style scoped>

</style>
