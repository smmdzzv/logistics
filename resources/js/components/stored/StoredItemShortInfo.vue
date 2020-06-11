<template>
    <div>
        <div>
            Дата: {{storedItem.created_at | luxon:format('dd-MM-yyyy')}}<br>
            Товар: {{storedItemInfo.item.name}}<br>
            {{`Вес: ${storedItemInfo.weight} кг`}} | {{`ШхВхД: ${storedItemInfo.width}x${storedItemInfo.height}x${storedItemInfo.length}`}}<br>
            {{`Владелец: ${storedItemInfo.owner.code}`}} <br>
            ID: {{storedItem.code}}<br>
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
    /*@page {*/
    /*    size: USER;*/
    /*    margin: 0;*/
    /*}*/

    /*@media print {*/
    /*    body {*/
    /*        margin: 0;*/
    /*        padding: 0;*/
    /*        font-weight: bold;*/
    /*        font-family: Arial !important;*/
    /*        font-size: 12px;*/
    /*    }*/
    /*}*/
</style>
