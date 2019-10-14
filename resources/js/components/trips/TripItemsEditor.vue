<template>
    <div class="container">
        <div class="row">
            <div class="col-lg-4 mb-4">
                <div class="card shadow">
                    <div class="card-header">
                        Детали рейса
                    </div>
                    <div class="card-body">
                        <p>Количество позиций: {{itemsCount}}</p>
                        <p>Суммарный вес: <span :class="{'text-danger': totalWeight > maxWeight}">{{totalWeight}}</span>
                            из {{maxWeight}} кг</p>
                        <p>Суммарная кубатура: <span
                            :class="{'text-danger': totalCubage > maxCubage}">{{totalCubage}}</span> из {{maxCubage}}
                            м<sup>3</sup>
                        </p>
                    </div>
                    <div class="card-footer">
                        <div class="form-group row mb-0">
                            <div class="col-md-6 offset-md-4">
                                <button :disabled="isSubmitting" @click="submit" class="btn btn-primary">
                                    Сохранить
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

            <div class="col-lg-8">
                <stored-table :branches="branches"
                              :items="trip.stored_items"
                              :prepareUrl="prepareUrl"
                              :selectedItems="storedItems"
                              @onItemSelected="onItemSelected"
                              @onItemUnselected="onItemUnselected"
                              class="shadow"
                              flowable
                              selectable/>
            </div>


        </div>
    </div>
</template>

<script>
    export default {
        name: "TripItemsEditor",
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
            // this.storedItems = this.trip.stored_items.filter(function () {
            //     return true;
            // });
        },
        data() {
            return {
                storedItems: this.trip.stored_items.filter(function () {
                    return true;
                }),
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
                    total += stored.info.weight * stored.info.height * stored.info.length;
                }
                return Math.round(total * 100) / 100;
            },
            maxWeight() {
                return this.trip.car.maxWeight
            },
            maxCubage() {
                return this.trip.car.maxCubage
            },
            action() {
                return '/trips/' + this.trip.id + '/items'
            }
        },
        methods: {
            onItemSelected(item) {
                this.storedItems.push(item)
                // this.storedItems.splice(0, this.storedItems.length);

                // this.storedItems = items.filter(function () {
                //     return true;
                // });
            },
            onItemUnselected(item) {
                this.storedItems = this.storedItems.filter(function (stored) {
                    return stored.id !== item.id
                })
            },
            async submit() {
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
            },
            prepareUrl(page, vm) {
                let action = `/trip/stored-items/available`;
                if (vm.selectedBranch)
                    action = `/trip/${vm.selectedBranch.id}/stored-items/available`;
                return action += '?paginate=7&page=' + page;
            }
        }
    }
</script>

<style scoped>

</style>
