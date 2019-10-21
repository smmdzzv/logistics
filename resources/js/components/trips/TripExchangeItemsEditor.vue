<template>
    <div class="col-12">
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
        >
            <template #header>
                <div class="row align-items-baseline">
                    <div class="ml-3 mb-3 mb-sm-0 mr-auto">
                         Перевести товары с рейса {{trip.code}}
                    </div>
                    <div class="ml-2">
                        <b-select :class="{'is-invalid': selectedTrip === null}" v-model="selectedTrip">
                            <option :value="null" disabled>-- Выберите рейс --</option>
                            <option :key="trip.id" :value="trip" v-for="trip in trips">
                                {{trip.code}}
                            </option>
                        </b-select>
                        <span class="invalid-feedback" role="alert">
                            <strong>Необходимо выбрать рейс</strong>
                        </span>
                    </div>
                </div>
            </template>
        </stored-items-table-card>
        <div class="row my-4">
            <div class="col-12 text-center">
                <button @click="submit" class="btn btn-primary">Сохранить</button>
            </div>
        </div>
    </div>
</template>

<script>
    import TableCardProps from '../common/TableCardProps.vue'
    import StoredItemsTableCard from "../stored/StoredItemsTableCard";

    export default {
        name: "TripExchangeItemsEditor",
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
            },
            trips: {
                type: Array,
                required: true
            }
        },
        data() {
            return {
                isSubmitting: false,
                isBusy: false,
                selectedItems: [],
                selectedTrip: null
            }
        },
        methods: {
            onItemsSelected(items) {
                this.selectedItems = items;
            },
            async submit() {
                if (!this.selectedBranch || this.selectedItems.length === 0)
                    return;
                try {
                    let data = {
                        storedItems: this.selectedItems.map((selected) => {
                            return selected.id;
                        }),
                        branch: this.selectedBranch.id
                    };

                    let action = `/trip/${this.trip.id}/exchange/stored-items`;
                    const response = await axios.post(action, data);
                    window.location = `/trips/${this.trip.id}`;
                } catch (e) {
                    this.$root.showErrorMsg(
                        'Ошибка сохранения',
                        'Не удалось принять товары. Повторите после обновления страницы'
                    );
                }
            }
        },
        components: {
            StoredItemsTableCard
        }
    }
</script>
