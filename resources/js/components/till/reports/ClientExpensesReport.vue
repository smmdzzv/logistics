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
        {{reportData}}
    </div>
</template>

<script>
    import {hideBusySpinner, showBusySpinner} from "../../../tools";

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
            fetchData: function () {
                if (!this.dateFrom || !this.client)
                    return;

                showBusySpinner();

                let action = `/reports/expenses/generate?client=${this.client.id}&dateFrom=${this.dateFrom}`
                if (this.dateTo)
                    action += `&dateTo=${this.dateTo}`;

                axios.get(action)
                    .then(response => this.reportData = response)
                    .catch(e => console.log(e))
                    .then(_ => hideBusySpinner())

            }
        },
        components: {
            'SearchUserDropdown': require('../../users/SearchUserDropdown.vue').default
        }
    }
</script>

<style scoped>

</style>
