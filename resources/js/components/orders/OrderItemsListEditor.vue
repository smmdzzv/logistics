<template>
    <div class="row">
        <div class="col-12 mb-3">
            <label class="col-12">Клиент</label>
            <search-user-dropdown
                :selected="clientSelected"
                autofocus
                placeholder="Введите ФИО или код клиента"
                url="/concrete/client/filter?userInfo=">
            </search-user-dropdown>
        </div>

        <div class="col-12 form-group">
            <label class="col-12">Выберите заказ</label>
            <b-form-select v-model="selectedOrder">
                <option disabled value="null">-- Выберите заказ --</option>
                <option :key="order.id" :value="order" v-for="order in orders">{{order.totalPrice}}$ от
                    {{order.created_at}}
                </option>
            </b-form-select>
        </div>

        <div class="col-12 pb-4">
            <stored-items-table-card
                :borderless="borderless"
                :fixed="fixed"
                :hover="hover"
                :responsive="responsive"
                :select-mode="selectMode"
                :selectable="selectable"
                :sticky-header="tableHeight"
                :storedItems="items"
                :striped="striped"
                @itemsSelected="onItemsSelected"
                title="Список товаров">
            </stored-items-table-card>
        </div>

        <div class="col-12 text-center">
            <button class="btn btn-primary">Выдать</button>
        </div>
    </div>

</template>

<script>
    import TableCardProps from '../common/TableCardProps.vue'

    export default {
        name: "OrderItemsListEditor",
        mixins: [TableCardProps],
        components: {
            'SearchUserDropdown': require('../users/SearchUserDropdown.vue').default,
            'StoredItemsTableCard': require('../stored/StoredItemsTableCard').default
        },
        data() {
            return {
                client: null,
                orders: [],
                selectedOrder: null,
                items: [],
                selectedItems: []
            }
        },
        methods: {
            clientSelected(client) {
                this.client = client
            },
            onItemsSelected(items) {
                this.selectedItems = items
            }
        },
        watch: {
            async client() {
                if (!this.client)
                    return;
                tShowSpinner();
                try {
                    const response = await getActiveOrders(this.client.id);
                    this.orders = response.data
                } catch (e) {
                    this.$root.showErrorMsg(
                        "Ошибка загрузки",
                        "Не удалось получить список активных заказов"
                    )
                }
                tHideSpinner();
            },
            async selectedOrder() {
                if (!this.selectedOrder)
                    return;
                tShowSpinner();
                try {
                    const response = await getOrderItems(this.selectedOrder.id);
                    this.items = response.data;
                } catch (e) {
                    this.$root.showErrorMsg(
                        "Ошибка загрузки",
                        "Не удалось получить список товаров"
                    )
                }
                tHideSpinner();
            }
        }
    }
</script>
