<template>
    <div class="container">
        <div class="row">
            <div class="col-lg-4 mb-4">
                <div class="card">
                    <div class="card-header">
                        Детали рейса
                    </div>
                    <div class="card-body">
                        <p>Количество позиций: {{storedItems.length}}</p>
                        <p>Количество товаров: {{itemsCount}}</p>
                        <p>Суммарный вес: <span :class="{'text-danger': totalWeight > maxWeight}">{{totalWeight}}</span> из {{maxWeight}}</p>
                        <p>Суммарная кубатура: <span :class="{'text-danger': totalCubage > maxCubage}">{{totalCubage}}</span> из {{maxCubage}}</p>
                    </div>
                    <div class="card-footer">
                        <div class="form-group row mb-0">
                            <div class="col-md-6 offset-md-4">
                                <button class="btn btn-primary" @click="submit" :disabled="isSubmitting">
                                    Сохранить
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

            <div class="col-lg-8">
                <stored-table :branches="branches"
                              :preselected="trip.stored_items"

                              @onItemsSelected="onItemsSelected"
                              flowablePagination
                              selectable/>
            </div>


        </div>
    </div>
</template>

<script>
    export default {
        name: "TripItemsEditor",
        props:{
            trip:{
                type:Object,
                required: true
            },
            branches:{
                type:Array,
                required:true
            }
        },
        data(){
            return{
                storedItems: this.trip.stored_items,
                isSubmitting:false
            }
        },
        computed: {
            itemsCount: function(){
                let count = 0;
                for(let stored of this.storedItems){
                    count += stored.count;
                }

                return count;
            },
            totalWeight(){
                let total = 0;
                for(let stored of this.storedItems){
                    total += stored.weight * stored.count;
                }

                return total;
            },

            totalCubage(){
                let total = 0;
                for(let stored of this.storedItems){
                    total += stored.weight * stored.height * stored.length;
                }

                return total;
            },
            maxWeight(){
                return this.trip.car.maxWeight
            },
            maxCubage(){
                return this.trip.car.maxCubage
            },
            action(){
                return '/trips/'+ this.trip.id +'/items'
            }
        },
        methods:{
            onItemsSelected(items) {
                this.storedItems.splice(0, this.storedItems.length);

                this.storedItems = items.filter(function () {
                    return true;
                });
            },
            async submit(){
                let data = {
                    storedItems: this.storedItems.map(function(item) {
                        return item.id;
                    })
                };
                try{
                    await axios.post('/stored/trip/' + this.trip.id, data);
                    window.location = getBaseUrl() + '/trips/' + this.trip.id;
                }
                catch (e) {
                    this.$root.showErrorMsg(
                        'Ошибка сохранения',
                        'Не удалось закрепить список товаров за рейсом. Повторите попытку после перезагрузки страницы.'
                    );
                }
            }
        }
    }
</script>

<style scoped>

</style>
