<template>
<div class="container">
    <div class="card">
        <div class="card-header bg-info">
            <div class="row">
                <div class="text-light col-sm-5 col-md-4 pt-2 h5">Список товаров</div>
                    <button class="btn btn-light offset-sm-4 offset-md-5  offset-lg-6" @click="showModal">Добавить</button>
            </div>
        </div>
        <ul class="list-group list-group-flush">
            <li class="list-group-item">Жесть</li>
            <li class="list-group-item">Бампер</li>
        </ul>
    </div>
    <stored-item-box :onItemAdded="onItemAdded"></stored-item-box>
</div>
</template>

<script>
    export default {
        name: "OrderItemsBox",
        data(){
            return{
                storedItems:[]
            }
        },
        methods:{
            showModal(){
                this.$bvModal.show('addItemModal');
            },
            onItemAdded(item){
                this.storedItems.push(item)
            }
        },
        components: {
            'StoredItemBox': require('./StoredItemBox').default
        }
    }
</script>

<style scoped>

</style>
