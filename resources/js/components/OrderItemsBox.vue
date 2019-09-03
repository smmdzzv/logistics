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
                        <div class="col-sm-4 col-md-6">Большой амортизатор</div>
                        <div class="col-sm-3 col-md-2">12,5 м<sup>3</sup></div>
                        <div class="col-sm-3 col-md-2">150 кг</div>

                        <div class="col-sm-2 col-md-2">
                            <img class="icon-btn-sm" src="/svg/delete.svg" alt="delete-item" @click="removeFromList()">
                        </div>
                    </div>
                </li>
                <li class="list-group-item" v-if="storedItems.length === 0">Для приема товара необходимо нажать кнопку
                    добавить
                </li>
            </ul>
        </div>
        <stored-item-box :onStoredItemAdded="onStoredItemAdded" :branch="user.branch" :tariffs="tariffs"></stored-item-box>
    </div>
</template>

<script>
    export default {
        name: "OrderItemsBox",
        props: {
            user: null,
            tariffs: Array
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
            getTotalWeight(stored) {
                if (!stored)
                    return null;
                let weight = stored.totalWeight = stored.weight * stored.count;
                return weight.toFixed(2)
            },
            getTotalCubage(stored) {
                if (!stored)
                    return null;
                let cubage = stored.totalCubage = stored.width * stored.length * stored.height * stored.count;
                return cubage.toFixed(2);
            },
            removeFromList(stored) {
                this.storedItems = jQuery.grep(this.storedItems, function (value) {
                    return value !== stored;
                })
            },

            //tariffPricing is attached to storedItem in @StoredItemBox.vue onAdded
            //tariffPricing properties are same as server version
            getTotalPrice(stored) {
                if(!stored)
                    return null;
                let sum = 0;

                let tariff = stored.tariffPricing;

                let weightPerCube = stored.totalWeight / stored.totalCubage;

                if(weightPerCube >= tariff.maxWeightPerCube){
                    sum = tariff.agreedPricePerKg * stored.totalWeight;
                    return sum.toFixed(2);
                }

                let price = tariff.pricePerCube;
                if(tariff.lowerLimit > 0 && weightPerCube <= tariff.lowerLimit)
                    price = price - tariff.discountForLowerLimit;
                else if(tariff.mediumLimit > 0 &&  weightPerCube <= tariff.mediumLimit)
                    price = price - tariff.discountForMediumLimit;
                else if(weightPerCube > tariff.upperLimit)
                    price = price +(weightPerCube - tariff.upperLimit) * tariff.pricePerExtraKg;


                sum = (price * stored.totalCubage);
                return sum.toFixed(2);
            },
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
