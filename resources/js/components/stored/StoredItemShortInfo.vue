<template>
    <div class="container border-bottom pt-3">
        <p>{{`ШхВхД: ${storedItem.width}x${storedItem.height}x${storedItem.length}`}} </p>
        <p>{{`Вес: ${storedItem.weight} кг`}} </p>
        <p>{{`Принят: ${storedItem.created_at.split(' ')[0]}`}} </p>
        <div style="width:100px">
            <barcode :value="storedItem.id" :options='{displayValue:false, height:50, width:1}' tag="svg"></barcode>
        </div>
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
