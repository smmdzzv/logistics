<template>
    <div class="container-fluid">
        <div class="row">
            <div class="form-group col-md-5 mb-3">
                <select v-model="trip" class="form-control">
                    <option :value="null">-- Выберите рейс --</option>
                    <option v-for="trip in activeTrips" :key="trip.id" :value="trip">
                        {{trip.code}}
                    </option>
                </select>
            </div>
            <button class="btn btn-primary mb-3" @click="submit">
                Загрузить на рейс
            </button>
        </div>
    </div>
</template>

<script>
    import {hideBusySpinner, showBusySpinner} from "../../tools";

    export default {
        name: "LoadCar",
        props: {
            storedItems: {
                type: Array,
                required: true
            }
        },
        mounted() {
            this.fetchActiveTrips()
        },
        data() {
            return {
                activeTrips: [],
                trip: null
            }
        },
        methods: {
            fetchActiveTrips() {
                showBusySpinner();
                axios.get('/trips/filtered?status=scheduled')
                    .then(response => this.activeTrips = response.data)
                    .finally(hideBusySpinner())
            },
            submit() {
                
            }
        }
    }
</script>

<style scoped>

</style>
