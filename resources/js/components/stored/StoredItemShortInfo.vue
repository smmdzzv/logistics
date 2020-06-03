<template>
    <div>
        <div>
            <span>{{storedItemInfo.item.name}}</span>
            <span>{{storedItem.code}}</span>
            <span>{{`ШхВхД: ${storedItemInfo.width}x${storedItemInfo.height}x${storedItemInfo.length}`}}
            | {{`Вес: ${storedItemInfo.weight} кг`}} </span>
<!--            <span>{{`Принят: ${storedItem.created_at}`}} </span>-->
            <span>{{`Владелец: ${storedItemInfo.owner.code}`}} </span>
        </div>

        <div>
            <barcode :options='{displayValue:false, height:40, width:1.5}' :value="storedItem.code" tag="svg"></barcode>
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
