<template>
    <table-card
        :fields="fields"
        :isBusy="isBusy"
        :items="items"
        excelFileName="Список валют"
        class="shadow"
        flowable
        primary-key="id"
        responsive
        striped>
        <template #header>
            Список валют
        </template>

        <template #footer>
            <div class="card-footer">
                <main-paginator :onPageChange="getItems"
                                :pagination="pagination"
                                flowable></main-paginator>
            </div>
        </template>
    </table-card>
</template>

<script>
    export default {
        name: "CurrenciesTable",
        mounted() {
            if (this.currencies)
                this.items = this.currencies;
            this.getItems();
        },
        props: {
            currencies: {
                type: Array,
                required: false
            },
        },
        data() {
            return {
                pagination: {
                    last_page: null,
                    current_page: null
                },
                items: [],
                isBusy: false,
                fields: [
                    {
                        key:'name',
                        label: 'Название',
                        sortable: true
                    },
                    {
                        key:'shortName',
                        label: 'Кор. обоз.',
                        sortable: true
                    },
                    {
                        key:'isoName',
                        label: 'ISO',
                        sortable: true
                    },
                    {
                        key:'country.name',
                        label: 'Страна',
                        sortable: true
                    }
                ]
            }
        },
        methods: {
            getItems(page = 1) {
                if (this.currencies)
                    return;

                this.isBusy = true;

                let action = '/currencies/all?paginate=10&page=' + page;

                axios.get(action)
                    .then(response => {
                        this.pagination = response.data;
                        if (this.flowablePagination)
                            response.data.data.forEach(item => {
                                this.items.push(item);
                            });
                        else
                            this.items = response.data.data;
                    })
                    .catch(error => {
                        this.$root.showErrorMsg(
                            'Ошибка загрузки',
                            'Не удалось загрузить список валют. Обновите страницу'
                        )
                    })
                    .finally(() => {
                        this.$nextTick(() => {
                            this.isBusy = false;
                        })
                    });

            },
        },
        components: {
            'MainPaginator': require('../../common/MainPaginator.vue').default,
            'TableCard': require('../../common/TableCard.vue').default
        }
    }
</script>

<style scoped>

</style>
