<template>
    <div>
        <div class="row">
            <button class="ml-auto mr-5 btn btn-sm btn-primary" @click="toggleTable">Переключить вид</button>
        </div>
        <div class="row p-3">
            <div class="col-12 mb-4">
                <div class="card">
                    <div class="card-header">
                        Детали рейса
                    </div>
                    <div class="card-body">
                        <p>Количество позиций: {{itemsCount}}</p>
                        <p>Суммарный вес: <span :class="{'text-danger': totalWeight > maxWeight}">{{totalWeight}}</span>
                            из {{maxWeight}} кг</p>
                        <p>Суммарная кубатура: <span
                                :class="{'text-danger': totalCubage > maxCubage}">{{totalCubage}}</span> из
                            {{maxCubage}}
                            м<sup>3</sup>
                        </p>
                        <small v-if="trip.hasTrailer" class="text-muted">С учетом кубатуры и грузоподъемности прицепа
                        </small>
                    </div>
                    <div class="card-footer">
                        <div class="form-group row mb-0">
                            <div class="col-md-6 offset-md-5">
                                <button :disabled="isSubmitting" @click="submit" class="btn btn-primary">
                                    Сохранить
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

            <div class="col-12" v-show="detailedMode">
                <stored-table
                        :branches="branches"
                        :prepareUrl="prepareUrl"
                        :providedItems="trip.storedItems"
                        :selectedItems="storedItems"
                        :tripId="trip.id"
                        listGenerator
                        @onItemSelected="onItemSelected"
                        @onItemUnselected="onItemUnselected"
                        @onItemsSelected="onItemsSelected"
                        class="shadow"
                        flowable
                        highlightRows
                        hover
                        selectable/>
            </div>
            <div class="col-12" v-show="!detailedMode">
                <stored-item-info-table :branches="branches"
                                        :providedStoredItems="trip.storedItems"
                                        :providedSelectedStoredItems="storedItems"
                                        :selectedItems="storedItems"
                                        @onItemsSelected="onItemsSelected"
                                        :columns-to-hide="['created_at', 'totalPrice']"
                                        url="/stored-item-info/filtered?trip=doesntHaveTrip&status=accepted&"
                                        flowable>

                </stored-item-info-table>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        name: "TripItemsListEditor",
        props: {
            trip: {
                type: Object,
                required: true
            },
            branches: {
                type: Array,
                required: true
            }
        },
        mounted() {
        },
        data() {
            return {
                storedItems: this.trip.storedItems.filter(function () {
                    return true;
                }),
                detailedMode: false,
                isSubmitting: false
            }
        },
        computed: {
            itemsCount: function () {
                return this.storedItems.length;
            },
            totalWeight() {
                let total = 0;
                for (let stored of this.storedItems) {
                    total += stored.info.weight;
                }

                return Math.round(total * 100) / 100;
            },

            totalCubage() {
                let total = 0;
                for (let stored of this.storedItems) {
                    total += stored.info.width * stored.info.height * stored.info.length;
                }
                return Math.round(total * 100) / 100;
            },
            maxWeight() {
                let weight = this.trip.car.maxWeight;
                if (this.trip.hasTrailer)
                    weight += this.trip.car.trailerMaxWeight;
                return weight
            },
            maxCubage() {
                let cubage = this.trip.car.maxCubage;
                if (this.trip.hasTrailer)
                    cubage += this.trip.car.trailerMaxCubage;
                return cubage;
            },
            action() {
                return '/trips/' + this.trip.id + '/items'
            }
        },
        methods: {
            toggleTable(){
                this.detailedMode = !this.detailedMode;
            },
            onItemSelected(item) {
                this.storedItems.push(item)
            },
            onItemsSelected(items) {
                this.storedItems = items;
            },
            onItemUnselected(item) {
                this.storedItems = this.storedItems.filter(function (stored) {
                    return stored.id !== item.id
                })
            },
            async submit() {
                tShowSpinner();
                let data = {
                    storedItems: this.storedItems.map(function (item) {
                        return item.id;
                    })
                };
                try {
                    const response = await axios.post('/trip/' + this.trip.id + '/stored-items', data);
                    window.location = getBaseUrl() + '/trips/' + this.trip.id;
                } catch (e) {
                    this.$root.showErrorMsg(
                        'Ошибка сохранения',
                        'Не удалось закрепить список товаров за рейсом. Повторите попытку после перезагрузки страницы.'
                    );
                }
                tHideSpinner();
            },
            prepareUrl(page, vm) {
                let action = `/trip/stored-items/available`;
                if (vm.selectedBranch)
                    action = `/trip/${vm.selectedBranch.id}/stored-items/available`;
                return action += '?paginate=40&page=' + page;
            }
        }
    }
</script>

<style scoped>

</style>
