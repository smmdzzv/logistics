<template>
    <div class="container">
        <div class="card">
            <div class="card-header bg-info">
                <div class="row">
                    <div class="text-light col-sm-5 col-md-4 pt-2 h5">Список тарифов</div>
                </div>
            </div>
            <div>
                <ul class="list-group list-group-flush">
                    <li class="list-group-item"
                        v-model="tariffs"
                        v-for="tariff in tariffs">
                        <div class="row" :key="tariff.id">
                            <div class="col-md-4"> {{tariff.name}}</div>
                            <div class="col-md-6"> {{tariff.description}}</div>
                            <div class="col-md-2">
                                <img class="icon-btn-sm" src="/svg/delete.svg" alt="delete-item"
                                     @click="removeFromList(tariff)">
                            </div>
                        </div>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        name: "TariffList",
        props: {
            tariffs: {
                type: Array,
                required: true
            }
        },
        methods: {
            removeFromList($tariff){
                this.storedItems = jQuery.grep(this.storedItems, function (value) {
                    return value !== $tariff;
                });
            }
        }
    }
</script>

<style scoped>

</style>
