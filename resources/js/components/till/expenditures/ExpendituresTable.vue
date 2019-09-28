<template>
    <div class="container">
        <table-card
            :customCells="customCells"
            :fields="fields"
            :isBusy="isBusy"
            :items="items"
            :selectable="selectable"
            :striped="striped"
            class="shadow"
            primary-key="id"
            responsive>
            <template #header>
                <div class="card-header">
                    Статьи расходов/доходов
                </div>
            </template>

            <template slot="type" slot-scope="{item}">
                <span v-if="item.type === 'in'">Доход</span>
                <span v-if="item.type === 'out'">Расход</span>
            </template>

            <template slot="type" slot-scope="{item}">
                <span v-if="item.type === 'in'">Доход</span>
                <span v-if="item.type === 'out'">Расход</span>
            </template>

            <template slot="edit" slot-scope="{item}">
                <a :href="getEditUrl(item)">
                    <img class="icon-btn-sm" src="/svg/edit.svg">
                </a>
            </template>

            <template slot="delete" slot-scope="{item}">
                <img @click="deleteItem(item)" class="icon-btn-sm" src="/svg/delete.svg">
            </template>

            <template #footer>
                <div class="card-footer">
                    <main-paginator :flowable="flowablePagination" :onPageChange="getExpenditures"
                                    :pagination="pagination"></main-paginator>
                </div>
            </template>
        </table-card>
    </div>
</template>

<script>
    export default {
        name: "ExpendituresTable",
        mounted() {
            if (this.expenditures)
                this.items = this.expenditures;
            this.getExpenditures();
        },
        props: {
            selectable: {
                type: Boolean,
                default: false
            },
            expenditures: {
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
                customCells: ['description', 'type', 'delete', 'edit'],
                fields: {
                    title: {
                        label: 'Статья',
                        sortable: true
                    },
                    description: {
                        label: 'Описание'
                    },
                    type: {
                        label: 'Тип',
                        sortable: true
                    },
                    'edit': {
                        label: ''
                    },
                    'delete': {
                        label: ''
                    }
                }
            }
        },
        methods: {
            getDetailsUrl(item) {
                return '/expenditures/' + item.id;
            },
            getExpenditures(page = 1) {
                if (this.trips)
                    return;

                this.isBusy = true;

                let action = '/expenditures/all?paginate=20&page=' + page;

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
                return '/expenditures/' + item.id + '/edit';
            },
            async deleteItem(item) {
                let confirm = await this.$bvModal.msgBoxConfirm(`Вы уверены что хотите удалить статью ${item.title}?`, {
                    centered: true,
                    okTitle: 'Да',
                    cancelTitle: 'Отменить',
                    footerClass: 'border-0',
                    title: 'Подтверждение удаления'
                });

                if(confirm){
                    try{
                        const response = await axios.delete('/expenditures/' + item.id);
                        this.items = this.items.filter(function (i) {
                            return i.id !== item.id
                        })
                    }
                    catch (e) {
                        console.log(e);
                    }
                }
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
