<template>
    <div>
        <stored-item-box :branches="branches"
                         :providedStoredItemInfo="storedItemInfoToEdit"
                         :onStoredItemAdded="onStoredItemAdded"
                         :tariffs="tariffs"
                         class="pb-4"></stored-item-box>
        <div class="card">
            <div class="card-header bg-info">
                <div class="row">
                    <div class="text-light col-6 col-md-4 h5">Список товаров</div>
                </div>
            </div>
            <table class="table table-striped table-responsive-md">
                <thead>
                <tr>
                    <th>№</th>
                    <th>Магазин</th>
                    <th>Товар</th>
                    <th>Объем</th>
                    <th>Вес</th>
                    <th>Кол-во</th>
                    <th>Кол-во мест</th>
                    <th>Цена за ед.</th>
                    <th>Сумма</th>
                    <th></th>
                </tr>
                </thead>
                <tbody>
                <tr v-for="(stored, index) in storedItems">
                    <td>{{index + 1}}</td>
                    <td>{{stored.shop}}</td>
                    <td>{{stored.item.name}}</td>
                    <td>{{getCubage(stored, true)}} м<sup>3</sup></td>
                    <td>{{getWeight(stored, true)}} кг</td>
                    <td>{{stored.count}}</td>
                    <td>{{stored.placeCount}}</td>
                    <td>{{getPriceForOne(stored)}} $</td>
                    <td>{{getPrice(stored)}} $</td>
                    <td>
                        <img @click="editItem(stored)" alt="delete-item" class="icon-btn-sm"
                             src="/svg/edit.svg">
                        <img @click="removeFromList(stored)" alt="delete-item" class="icon-btn-sm"
                             src="/svg/delete.svg">
                    </td>
                </tr>
                </tbody>
                <tfoot>
                <tr>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td>{{getTotalCubage()}} м<sup>3</sup></td>
                    <td>{{getTotalWeight()}} кг</td>
                    <td>{{getTotalCount()}}</td>
                    <td>{{getPlaceTotalCount()}}</td>
                    <td>{{getAveragePriceForOne()}} $</td>
                    <td>{{getTotalPrice()}} $</td>
                    <td></td>
                </tr>
                </tfoot>
            </table>
        </div>
    </div>
</template>

<script>
    export default {
        name: "OrderItemsBox",
        components: {
            'StoredItemBox': require('../stored/StoredItemBox').default
        },
        mounted() {
            if (this.order)
                for (let i = 0; i < this.order.storedItemInfos.length; i++) {
                    this.onStoredItemAdded(this.order.storedItemInfos[i]);
                }
        },
        props: {
            user: null,
            tariffs: Array,
            order: Object,
            branches: Array
        },
        data() {
            return {
                storedItems: [],
                storedItemInfoToEdit: null
            }
        },
        methods: {
            showModal() {
                this.$bvModal.show('addItemModal');
            },
            onStoredItemAdded(storedItem) {
                if (storedItem.id)
                    this.storedItems = this.storedItems.filter(function (el) {
                        return el.id !== storedItem.id
                    });
                this.storedItems.push(storedItem);
                this.$emit('onStoredItemsChange', this.storedItems)
            },
            getWeight(stored, fixedResult) {
                if (!stored)
                    return null;
                let weight = stored.totalWeight = stored.weight * stored.count;
                return fixedResult ? weight.toFixed(3) : weight;
            },
            getCubage(stored, fixedResult) {
                if (!stored)
                    return null;
                let cubage = stored.totalCubage = stored.width * stored.length * stored.height * stored.count;
                return fixedResult ? cubage.toFixed(3) : cubage;
            },
            removeFromList(stored) {
                this.storedItems = jQuery.grep(this.storedItems, function (value) {
                    return value !== stored;
                });
                this.$emit('onStoredItemsChange', this.storedItems);
            },
            editItem(stored) {
                this.storedItemInfoToEdit = $.extend(true, {}, stored);
                if (!stored.id)
                    this.removeFromList(stored);
            },
            //tariffPricing is attached to storedItem in @StoredItemBox.vue onAdded
            //tariffPricing properties are same as server version
            getPrice(stored) {
                if (!stored)
                    return null;

                if (stored.customPrice) {
                    return stored.customPrice.toFixed(2);
                }


                let tariff = stored.billingInfo.tariffPricing;

                let weightPerCube = stored.totalWeight / stored.totalCubage;

                if (stored.item.onlyAgreedPrice || weightPerCube >= tariff.maxWeightPerCube && stored.item.calculateByNormAndWeight) {
                    let price = tariff.agreedPricePerKg * stored.totalWeight;
                    price = Math.round(price * 100) / 100;
                    return price
                }

                let price = tariff.pricePerCube;
                if (stored.item.applyDiscount) {
                    if (tariff.lowerLimit > 0 && weightPerCube <= tariff.lowerLimit)
                        price = price - tariff.discountForLowerLimit;
                    else if (tariff.mediumLimit > 0 && weightPerCube <= tariff.mediumLimit)
                        price = price - tariff.discountForMediumLimit;
                }

                if (weightPerCube > tariff.upperLimit)
                    price = price + (weightPerCube - tariff.upperLimit) * tariff.pricePerExtraKg;

                price = price * stored.totalCubage;
                price = Math.round(price * 100) / 100;
                return price;
            },
            getPriceForOne(stored) {
                let price = this.getPrice(stored) / (stored.count * stored.placeCount);
                price = Math.ceil(price * 100) / 100
                return price.toFixed(2);
            },
            getAveragePriceForOne() {
                let average = this.storedItems.reduce((sum, item) => sum + Number(this.getPriceForOne(item)), 0) / this.storedItems.length;
                if(!average)
                    average = 0;
                return average.toFixed(2);
            },
            getTotalPrice() {
                let sum = 0;
                for (let stored of this.storedItems) {
                    let price = this.getPrice(stored);
                    sum += Number(price);
                }
                return sum.toFixed(2);
            },
            getTotalWeight() {
                let totalWeight = 0;
                for (let stored of this.storedItems) {
                    let weight = this.getWeight(stored, false);
                    totalWeight += weight;
                }
                return totalWeight.toFixed(3);
            },
            getTotalCubage() {
                let totalCubage = 0;
                for (let stored of this.storedItems) {
                    let cubage = this.getCubage(stored, false);
                    totalCubage += cubage;
                }
                return totalCubage.toFixed(3);
            },
            getTotalCount() {
                return this.storedItems.reduce((sum, item) => sum + item.count, 0)
            },
            getPlaceTotalCount() {
                return this.storedItems.reduce((sum, item) => sum + item.count * item.placeCount, 0)
            },
        }
    }
</script>
