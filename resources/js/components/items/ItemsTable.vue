<template>
    <div class="container">
        <table-card
            :customCells="customCells"
            :fields="fields"
            :isBusy="isBusy"
            :items="items"
            excelFileName="Список наименований"
            class="shadow"
            primary-key="id"
            responsive
            striped>
            <template #header>
                <div class="row align-items-baseline">
                    <div class="col-md-4 mb-2 mb-md-0">Список наименований</div>
                    <div class="col-md-8 text-md-right"><a class="btn btn-primary" href="/items/create">Добавить</a>
                    </div>
                </div>
            </template>

            <template slot="onlyCustomPrice" slot-scope="{item}">
                <b-check :checked="item.onlyCustomPrice" disabled></b-check>
            </template>

            <template slot="onlyAgreedPrice" slot-scope="{item}">
                <b-check :checked="item.onlyAgreedPrice" disabled></b-check>
            </template>

            <template slot="calculateByNormAndWeight" slot-scope="{item}">
                <b-check :checked="item.calculateByNormAndWeight" disabled></b-check>
            </template>

            <template slot="applyDiscount" slot-scope="{item}">
                <b-check :checked="item.applyDiscount" disabled></b-check>
            </template>

            <template slot="buttons" slot-scope="{item}">
                <div class="d-flex">
                    <a class="mr-2" :href="'/items/' + item.id + '/edit'">
                        <img class="icon-btn-sm" src="/svg/edit.svg">
                    </a>
                    <a href="#" @click.prevent="deleteItem(item)">
                        <img class="icon-btn-sm" src="/svg/delete.svg">
                    </a>
                </div>
            </template>

            <template #footer>
                <div class="card-footer">
                    <main-paginator :flowable="flowable" :onPageChange="getItems"
                                    :pagination="pagination"></main-paginator>
                </div>
            </template>
        </table-card>
    </div>
</template>

<script>
    import {hideBusySpinner, showBusySpinner} from "../../tools";

    export default {
        name: "ItemsTable",
        mounted() {
            this.getItems();
        },
        props: {
            flowable: {
                type: Boolean,
                default: true
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
                customCells: ['applyDiscount', 'onlyCustomPrice', 'onlyAgreedPrice', 'calculateByNormAndWeight', 'buttons'],
                fields: {
                    name: {
                        label: 'Наименование',
                        sortable: true
                    },
                    unit: {
                        label: 'Единица',
                        sortable: false
                    },
                    // 'branch.name': {
                    //     label: 'Филиал',
                    //     sortable: true
                    // },
                    onlyCustomPrice: {
                        label: 'Ручная цена',
                        sortable: true
                    },
                    onlyAgreedPrice: {
                        label: 'Дог-ная цена',
                        sortable: true
                    },
                    calculateByNormAndWeight: {
                        label: 'Расчет по норме и весу',
                        sortable: true
                    },
                    applyDiscount: {
                        label: 'Учитывать скидку',
                        sortable: true
                    },
                    buttons: {
                        label: ''
                    }
                }
            }
        },
        methods: {
            getItems(page = 1) {
                if (this.trips)
                    return;

                this.isBusy = true;

                let action = '/items/all?paginate=10&page=' + page;

                axios.get(action)
                    .then(response => {
                        this.pagination = response.data;
                        if (this.flowable)
                            response.data.data.forEach(item => {
                                this.items.push(item);
                            });
                        else
                            this.items = response.data.data;
                        this.$nextTick(() => {
                            this.isBusy = false;
                        })
                    });
            },
            deleteItem(item) {
                this.$bvModal.msgBoxConfirm('Вы действительно хотите удалить наименование '
                    + item.name + '?', {
                    okTitle: 'Удалить',
                    okVariant: 'danger',
                    cancelTitle: 'Отмена',
                    centered: true
                })
                    .then(confirm => {
                        if (confirm) {
                            showBusySpinner();
                            axios.post('/items/' + item.id,{
                                _method:'delete'
                            })
                                .then(_ => {
                                    this.$bvToast.toast('Наименование "'
                                        + item.name + '" удалено', {
                                        title: 'Успешно удалено',
                                        autoHideDelay: 5000,
                                        appendToast: true,
                                        variant: 'success'
                                    });

                                    this.items = this.items.filter(i => i.id !== item.id)
                                })
                                .catch(e => {
                                    let message = 'Не удалось удалить наименование';

                                    if (e.response.status === 422)
                                        message = e.response.data.message

                                    this.$bvToast.toast(message, {
                                        title: 'Ошибка удаления',
                                        autoHideDelay: 5000,
                                        appendToast: true,
                                        variant: 'danger'
                                    });
                                })
                                .then(_ => this.$nextTick(_ => hideBusySpinner()))
                        }
                    })
                    .catch(err => {
                        // An error occurred
                    })
            }
        },
        computed: {
            currentPage() {
                return this.pagination.current_page;
            },
            lastPage() {
                return this.pagination.last_page
            }
        },
        components: {
            'MainPaginator': require('../common/MainPaginator.vue').default,
            'TableCard': require('../common/TableCard.vue').default
        }
    }
</script>
