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
                    <div class="ml-2 mb-3 mb-sm-0 mr-auto">
                        Принять товары с рейса
                    </div>
                    <div class="ml-2">
                        <b-select :class="{'is-invalid': selectedBranch === null}" v-model="selectedBranch">
                            <option :value="null" disabled>--Выберите филиал--</option>
                            <option :key="branch.id" :value="branch" v-for="branch in branches">{{branch.name}}
                            </option>
                        </b-select>
                        <span class="invalid-feedback" role="alert">
                            <strong>Необходимо выбрать филиал</strong>
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
        name: "UnloadedItemsEditor",
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
            branches: {
                type: Array,
                required: true
            }
        },
        data() {
            return {
                isSubmitting: false,
                isBusy: false,
                selectedItems: [],
                selectedBranch: null
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

                    let action = `/trip/${this.trip.id}/stored-items/unload`;
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
