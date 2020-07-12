<template>
    <div class="container">
        <table-card
            :fields="fields"
            :isBusy="isBusy"
            :items="items"
            :selectable="selectable"
            :striped="striped"
            excelFileName="Статьи расходов и доходов"
            class="shadow"
            primary-key="id"
            responsive>
            <template #header>
                Статьи расходов/доходов
            </template>

            <template v-slot:cell(type)="{item}">
                <span v-if="item.type === 'in'">Доход</span>
                <span v-if="item.type === 'out'">Расход</span>
            </template>

            <template v-slot:cell(type)="{item}">
                <span v-if="item.type === 'in'">Доход</span>
                <span v-if="item.type === 'out'">Расход</span>
            </template>

            <template v-slot:cell(edit)="{item}">
                <a :href="getEditUrl(item)">
                    <img class="icon-btn-sm" src="/svg/edit.svg">
                </a>
            </template>

            <template v-slot:cell(delete)="{item}">
                <img @click="deleteItem(item)" class="icon-btn-sm" src="/svg/delete.svg">
            </template>

            <template #footer>
                <div class="card-footer">
                    <main-paginator :flowable="flowablePagination" :onPageChange="getPaymentItems"
                                    :pagination="pagination"></main-paginator>
                </div>
            </template>
        </table-card>
    </div>
</template>

<script>
    export default {
        name: "PaymentItemsTable",
        mounted() {
            if (this.paymentItems)
                this.items = this.paymentItems;
            this.getPaymentItems();
        },
        props: {
            selectable: {
                type: Boolean,
                default: false
            },
            paymentItems: {
                type: Array,
                required: false
            },
            flowablePagination: {
                type: Boolean,
                default: false
            },
            striped: {
                type: Boolean,
                default: false
            }
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
                        key: 'title',
                        label: 'Статья',
                        sortable: true
                    },
                    {
                        key: 'description',
                        label: 'Описание'
                    },
                    {
                        key: 'edit',
                        label: ''
                    },
                    {
                        key: 'delete',
                        label: ''
                    }
                ]
            }
        },
        methods: {
            getDetailsUrl(item) {
                return '/payment-items/' + item.id;
            },
            getPaymentItems(page = 1) {
                if (this.trips)
                    return;

                this.isBusy = true;

                let action = '/payment-items/all?paginate=20&page=' + page;

                axios.get(action)
                    .then(response => {
                        this.pagination = response.data;
                        if (this.flowablePagination)
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
            getEditUrl(item) {
                return '/payment-items/' + item.id + '/edit';
            },
            async deleteItem(item) {

                let confirm = await this.$bvModal.msgBoxConfirm(`Вы уверены что хотите удалить статью ${item.title}?`, {
                    centered: true,
                    okTitle: 'Да',
                    cancelTitle: 'Отменить',
                    footerClass: 'border-0',
                    title: 'Подтверждение удаления'
                });
                this.$bvModal.show('busyModal');
                if (confirm) {
                    try {
                        const response = await axios.delete('/payment-items/' + item.id);
                        this.items = this.items.filter(function (i) {
                            return i.id !== item.id
                        })
                    } catch (e) {
                        console.log(e);
                    }
                }
                this.$nextTick(
                    () => {
                        this.$bvModal.hide('busyModal');
                    }
                )
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
            'MainPaginator': require('../../common/MainPaginator.vue').default,
            'TableCard': require('../../common/TableCard.vue').default
        }
    }
</script>
