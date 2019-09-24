<template>
    <div v-if="lastPage > 1">
        <div v-if="flowable && lastPage > currentPage" class="text-center">
            <button @click="pageChange" class="btn btn-outline-primary">
                Загрузить еще
            </button>
        </div>
        <div v-if="!flowable">
            <pagination :data="pagination" @pagination-change-page="pageChange"/>
        </div>
    </div>
</template>

<script>
    export default {
        name: "MainPaginator",
        props: {
            pagination: {
                type: Object,
                required: true
            },
            onPageChange: {
                type: Function,
                required: true
            },
            flowable: {
                type: Boolean,
                default: false
            },
        },
        methods:{
            pageChange(page){
                if(this.flowable) page = this.currentPage + 1;
                if(page !== this.pagination.current_page)
                    this.onPageChange(page);
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
            'Pagination': require('laravel-vue-pagination')
        }
    }
</script>

<style scoped>

</style>
