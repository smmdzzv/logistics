<template>
    <div class="container">
        <div class="card">
            <div class="card-header bg-info">
                <div class="row">
                    <div class="text-light col-sm-5 col-md-4 pt-2 h5">Список товаров</div>
                    <button class="btn btn-light offset-sm-4 offset-md-5  offset-lg-6" @click="showModal">Добавить
                    </button>
                </div>
            </div>
            <ul class="list-group list-group-flush">
                <li class="list-group-item"
                    v-model="storedItems"
                    v-for="stored in storedItems">
                    <div class="row" :key="stored.id">
                        <div class="col-md-4"> {{stored.item.name}}</div>
                        <div class="col-md-2"> {{getTotalCubage(stored)}} м<sup>3</sup></div>
                        <div class="col-md-2"> {{getTotalWeight(stored)}} кг</div>
                        <div class="col-md-2"> {{getTotalPrice(stored)}} $</div>
                        <div class="col-md-2">
                            <img class="icon-btn-sm" src="/svg/delete.svg" alt="delete-item"
                                 @click="removeFromList(stored)">
                        </div>
                    </div>
                </li>
                <li class="list-group-item">
                    <div class="row">
                        <div class="col-md-4">Большой амортизатор</div>
                        <div class="col-md-2">12,5 м<sup>3</sup></div>
                        <div class="col-md-2">150 кг</div>
                        <div class="col-md-2">{{this.getTotalPrice()}} $</div>
                        <div class="col-md-2">
                            <img class="icon-btn-sm" src="/svg/delete.svg" alt="delete-item" @click="removeFromList()">
                        </div>
                    </div>
                </li>
                <li class="list-group-item" v-if="storedItems.length === 0">Для приема товара необходимо нажать кнопку
                    добавить
                </li>
            </ul>
        </div>
        <stored-item-box :onStoredItemAdded="onStoredItemAdded" :branch="user.branch"></stored-item-box>
    </div>
</template>

<script>
    export default {
        name: "OrderItemsBox",
        props: {
            user: null
        },
        data() {
            return {
                storedItems: []
            }
        },
        methods: {
            showModal() {
                this.$bvModal.show('addItemModal');
            },
            onStoredItemAdded(storedItem) {
                this.storedItems.push(storedItem)
            },
            getTotalWeight(item) {
                if (!item)
                    return null;
                return item.weight * item.count;
            },
            getTotalCubage(item) {
                if (!item)
                    return null;
                return item.width * item.length * item.height * item.count;
            },
            getTotalPrice() {
                return 0
            },
            removeFromList(item) {
                this.storedItems = jQuery.grep(this.storedItems, function (value) {
                    return value !== item;
                })
            }
        },
        components: {
            'StoredItemBox': require('./StoredItemBox').default
        }
    }
</script>

<style scoped>
    .icon-btn-sm {
        width: 18px;
    }
</style>
