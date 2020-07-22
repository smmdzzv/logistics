<template>
    <table-card
    :isBusy="isBusy"
    :fields="fields"
    :items="items"
    :striped="striped"
    excelFileName="Список рейсов"
    class="shadow"
    responsive
    primary-key="id">
        <template #header>
                Список рейсов
        </template>

        <template v-slot:cell(buttons)="{item}">
            <div class="d-flex">
                <a class="btn" :href="getDetailsUrl(item)">
                    <img class="icon-btn-sm" src="/svg/file.svg">
                </a>

                <a class="btn ml-1" :href="`/trips/${item.id}/edit`">
                    <img class="icon-btn-sm" src="/svg/edit.svg">
                </a>
            </div>
        </template>

        <template v-slot:cell(departureDate)="{item}">
            <span> {{item.departureDate | luxon:format('dd-MM-yyyy')}} </span>
        </template>

        <template v-slot:cell(returnDate)="{item}">
            <span> {{item.returnDate | luxon:format('dd-MM-yyyy')}} </span>
        </template>

        <template #footer>
            <div class="card-footer">
                <main-paginator :pagination="pagination" :onPageChange="getTrips" :flowable="flowable" ></main-paginator>
            </div>
        </template>
    </table-card>
</template>

<script>
    export default {
        name: "TripsTable",
        mounted() {
            if(this.trips)
                this.items = this.trips;
            this.getTrips();
        },
        props: {
            selectable: {
                type: Boolean,
                default: false
            },
            trips:{
                type: Array,
                required:false
            },
            flowable:{
                type: Boolean,
                default: false
            },
            striped:{
                type:Boolean,
                default:false
            }
        },
        data() {
            return {
                pagination: {
                    last_page: null,
                    current_page: null
                },
                items:[],
                isBusy: false,
                fields: [
                    {
                        key:'code',
                        label: 'Номер',
                        sortable: true
                    },
                    {
                        key:'car.number',
                        label: 'Машина',
                        sortable: true
                    },
                    {
                        key:'driver.name',
                        label: 'Водитель',
                        sortable: true
                    },
                    {
                        key:'departureDate',
                        label: 'Дата отправления',
                        sortable: true
                    },
                    {
                        key:'returnDate',
                        label: 'Дата возрващения',
                        sortable: true
                    },
                    {
                        key:'buttons',
                        label:''
                    }
                ]
            }
        },
        methods: {
            getDetailsUrl(item){
                return '/trips/' + item.id;
            },
            getTrips(page = 1) {
                if(this.trips)
                    return;

                this.isBusy = true;

                let action = '/trips/all?paginate=20&page=' + page;

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
