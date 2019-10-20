<template>
    <div class="container border-bottom pt-3">
        <div class="container">
            <p>{{`Тип: ${storedItemInfo.item.name}`}}</p>
            <p>{{`Код: ${storedItem.code}`}}</p>
            <p>{{`ШхВхД: ${storedItemInfo.width}x${storedItemInfo.height}x${storedItemInfo.length}`}}</p>
            <p>{{`Вес: ${storedItemInfo.weight} кг`}} </p>
            <p>{{`Принят: ${storedItem.created_at}`}} </p>
        </div>

        <div style="width:100px">
            <barcode :options='{displayValue:false, height:50, width:1}' :value="storedItem.code" tag="svg"></barcode>
        </div>
    </div>
</template>

<script>
    export default {
        name: "StoredItemShortInfo",
        props: {
            storedItemInfo: {
                type: Object,
                required: true
            },
            storedItem: {
                type: Object,
                required: true
            }
        }
    }
</script>

<style scoped>

</style>
