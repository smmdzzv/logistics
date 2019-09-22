<template>
    <div class="container border-bottom pt-3">
        <p>{{`ШхВхД: ${storedItem.width}x${storedItem.height}x${storedItem.length}`}} </p>
        <p>{{`Вес: ${storedItem.weight} кг`}} </p>
        <p>{{`Принят: ${storedItem.created_at.split(' ')[0]}`}} </p>
        <barcode :value="storedItem.id" :options='{displayValue:false, height:25}'></barcode>
    </div>
</template>

<script>
    export default {
        name: "StoredItemShortInfo",
        props:{
            storedItem: {type: Object, required: true}
        }
    }
</script>

<style scoped>

</style>
