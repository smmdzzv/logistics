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
            <div>
                <ul class="list-group list-group-flush">
                    <li class="list-group-item"
                        v-model="storedItems"
                        v-for="stored in storedItems">
                        <div class="row" :key="stored.id">
                            <div class="col-md-4"> {{stored.item.name}}</div>
                            <div class="col-md-2"> {{getCubage(stored, true)}} м<sup>3</sup></div>
                            <div class="col-md-2"> {{getWeight(stored, true)}} кг</div>
                            <div class="col-md-2"> {{getPrice(stored)}} $</div>
                            <div class="col-md-2">
                                <img class="icon-btn-sm" src="/svg/delete.svg" alt="delete-item"
                                     @click="removeFromList(stored)">
                            </div>
                        </div>
                    </li>

                    <li class="list-group-item" v-if="storedItems.length === 0">Для приема товара необходимо нажать кнопку
                        добавить
                    </li>

                </ul>
            </div>
            <div class="card-footer" v-if="storedItems.length > 0">
                <div class="row" >
                    <div class="col-md-4"> Итого</div>
                    <div class="col-md-2" :property="storedItems">{{getTotalCubage()}} м<sup>3</sup> </div>
                    <div class="col-md-2" :property="storedItems"> {{getTotalWeight()}} кг</div>
                    <div class="col-md-2" :property="storedItems">{{getTotalPrice()}} $</div>
                    <div class="col-md-2"> </div>
                </div>
            </div>
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
                this.storedItems.push(storedItem);
                this.$emit('onStoredItemsChange', this.storedItems)
            },
            getWeight(stored, fixedResult) {
                if (!stored)
                    return null;
                let weight = stored.totalWeight = stored.weight * stored.count;
                return fixedResult? weight.toFixed(2) : weight;
            },
            getCubage(stored, fixedResult) {
                if (!stored)
                    return null;
                let cubage = stored.totalCubage = stored.width * stored.length * stored.height * stored.count;
                return fixedResult? cubage.toFixed(2) : cubage;
            },
            removeFromList(stored) {
                this.storedItems = jQuery.grep(this.storedItems, function (value) {
                    return value !== stored;
                });
                this.$emit('onStoredItemsChange', this.storedItems);
            },

            //tariffPricing is attached to storedItem in @StoredItemBox.vue onAdded
            //tariffPricing properties are same as server version
            getPrice(stored) {
                if(!stored)
                    return null;
                let tariff = stored.tariffPricing;

                let weightPerCube = stored.totalWeight / stored.totalCubage;

                if(weightPerCube >= tariff.maxWeightPerCube){
                    stored.price = tariff.agreedPricePerKg * stored.totalWeight;
                    stored.price = Math.round(stored.price*100)/100;
                    return stored.price
                }

                let price = tariff.pricePerCube;
                if(tariff.lowerLimit > 0 && weightPerCube <= tariff.lowerLimit)
                    price = price - tariff.discountForLowerLimit;
                else if(tariff.mediumLimit > 0 &&  weightPerCube <= tariff.mediumLimit)
                    price = price - tariff.discountForMediumLimit;
                else if(weightPerCube > tariff.upperLimit)
                    price = price +(weightPerCube - tariff.upperLimit) * tariff.pricePerExtraKg;


                stored.price = price * stored.totalCubage;
                stored.price = Math.round(stored.price*100)/100;
                return stored.price;
            },
            getTotalPrice(){
                let sum = 0;
                for(let stored of this.storedItems){
                    let price = stored.price;
                    if(price)
                        sum += price;
                    else
                        sum += this.getPrice(stored);
                }
                return sum.toFixed(2);
            },
            getTotalWeight(){
                let totalWeight = 0;
                for(let stored of this.storedItems){
                    let weight = this.getWeight(stored, false);
                    totalWeight += weight;
                }
                return totalWeight.toFixed(2);
            },
            getTotalCubage(){
                let totalCubage = 0;
                for(let stored of this.storedItems){
                    let cubage = this.getCubage(stored, false);
                    totalCubage += cubage;
                }
                return totalCubage.toFixed(2);
            }
        },
        components: {
            'StoredItemBox': require('../stored/StoredItemBox').default
        }
    }
</script>

<style scoped>

</style>
