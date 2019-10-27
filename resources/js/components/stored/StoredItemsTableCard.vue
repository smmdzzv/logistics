<template>
    <table-card
        :borderless="borderless"
        :fields="fields"
        :fixed="fixed"
        :hover="hover"
        :items="items"
        :responsive="responsive"
        :select-mode="selectMode"
        :selectable="selectable"
        :sticky-header="tableHeight"
        :striped="striped"
        :tableBusy="isBusy"
        @itemsSelected="onItemsSelected"
        excelFileName="Список товаров рейса"
        primaryKey="id"
        responsive>

        <template #header>
            <slot name="header">
                {{title}}
            </slot>
        </template>
    </table-card>
</template>

<script>
    import TableCardProps from '../common/TableCardProps.vue'

    export default {
        name: "StoredItemsTableCard", mixins: [TableCardProps],
        mounted() {
            if (this.storedItems)
                this.items = this.storedItems;
        },
        props: {
            storedItems: {
                type: Array,
                required: true
            },
            title: {
                type: String
            },
        },
        data() {
            return {
                items: [],
                isSubmitting: false,
                isBusy: this.tableBusy,
                selectedItems: [],
                fields: {
                    'info.item.name': {
                        label: 'Имя',
                        sortable: true
                    },
                    'info.width': {
                        label: 'Ширина',
                        sortable: true
                    },
                    'info.height': {
                        label: 'Высота',
                        sortable: true
                    },
                    'info.length': {
                        label: 'Длина',
                        sortable: true
                    },
                    'info.weight': {
                        label: 'Вес',
                        sortable: true
                    },
                    'info.owner.name': {
                        label: 'Владелец',
                        sortable: true
                    },
                    'storage_history.storage.name': {
                        label: 'Склад'
                    },
                    'selected': {
                        label: ''
                    }
                }
            }
        },
        methods: {
            isSelected(item) {
                return this.selectedItems.find(function (selected) {
                    return selected.id === item.id;
                });
            },
            onItemsSelected(items) {
                this.$emit('itemsSelected', items)
            }
        },
        watch: {
            storedItems() {
                this.items = this.storedItems
            }
        },
        components: {
            'TableCard': require('../common/TableCard.vue').default
        }
    }
</script>

<style scoped>

</style>
