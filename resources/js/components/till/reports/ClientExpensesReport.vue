<template>
    <div>
        <div class="row px-2">
            <div class="form-group col-md-4">
                <label>Клиент</label>
                <search-user-dropdown url="/concrete/client/filter?userInfo="
                                      :selected="onClientSelected"
                                      autofocus></search-user-dropdown>
            </div>
            <div class="form-group col-md-4">
                <label>Дата начала</label>
                <input class="form-control" type="date" v-model="dateFrom">
            </div>
            <div class="form-group col-md-4">
                <label>Дата конца</label>
                <input class="form-control" type="date" v-model="dateTo">
            </div>
        </div>
        <div class="row">
            <button class="mx-auto btn btn-primary" @click="fetchData">Сформировать отчет</button>
        </div>
    </div>
</template>

<script>
    import {showBusySpinner, hideBusySpinner} from "../../../tools";

    export default {
        name: "ClientExpensesReport",
        data() {
            return {
                dateFrom: null,
                dateTo: null,
                client: null,
                reportData: null
            }
        },
        methods: {
            onClientSelected(client) {
                this.client = client;
            },
            fetchData() {
                showBusySpinner();
                axios.get('/reports/expenses/generate')
                    .then(response => this.reportData = response)
                    .catch(e => console.log(e))
                    .then(hideBusySpinner())

            }
        },
        components: {
            'SearchUserDropdown': require('../../users/SearchUserDropdown.vue').default
        }
    }
</script>

<style scoped>

</style>
