<template>
    <div>
        <stored-items-table-card
            :borderless="borderless"
            :fixed="fixed"
            :hover="hover"
            :responsive="responsive"
            :select-mode="selectMode"
            :selectable="selectable"
            :sticky-header="tableHeight"
            :storedItems="storedItems"
            :striped="striped"
            :tableBusy="isBusy"
            :title="title"
            @itemsSelected="onItemsSelected"
        />
        <div class="row my-4">
            <div class="col-12 text-center">
                <button class="btn btn-primary" @click="submit">Сохранить</button>
            </div>
        </div>
    </div>
</template>

<script>
    import TableCardProps from '../common/TableCardProps.vue'
    import StoredItemsTableCard from "../stored/StoredItemsTableCard";

    export default {
        name: "LoadedItemsEditor",
        mixins: [TableCardProps],
        props: {
            trip: {
                type: Object,
                required: true
            },
            storedItems: {
                type: Array,
                required: true
            },
            title: {
                type: String
            }
        },
        data() {
            return {
                isSubmitting: false,
                isBusy: false,
                selectedItems: [],

            }
        },
        methods: {
            onItemsSelected(items) {
                this.selectedItems = items;
            },
            async submit(){
                try{
                    let data = this.selectedItems.map((selected)=>{
                        return selected.id;
                    });

                    let action = `/trip/${this.trip.id}/stored-items/load`;
                    const response = await  axios.post(action, data);
                    window.location = `/trips/${this.trip.id}`;
                }
                catch (e) {
                    this.$root.showErrorMsg(
                        'Ошибка сохранения',
                        'Не удалось загрузить товары на рейс. Повторите после обновления страницы'
                    );
                }
            }
        },
        components: {
            StoredItemsTableCard,
            'TableCard': require('../common/TableCard.vue').default
        }
    }
</script>
