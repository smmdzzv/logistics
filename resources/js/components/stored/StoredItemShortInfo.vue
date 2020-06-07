<template>
    <div>
        <div>
            <span>Дата: {{storedItem.created_at | luxon:format('dd-MM-yyyy')}}</span><br>
            <span>Товар: {{storedItemInfo.item.name}}</span><br>
            <span>{{`Вес: ${storedItemInfo.weight} кг`}} | {{`ШхВхД: ${storedItemInfo.width}x${storedItemInfo.height}x${storedItemInfo.length}`}}</span><br>
            <span>{{`Владелец: ${storedItemInfo.owner.code}`}} </span><br>
            <span>ID: {{storedItem.code}}</span><br>
        </div>

        <div style="margin-left: -8px">
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
