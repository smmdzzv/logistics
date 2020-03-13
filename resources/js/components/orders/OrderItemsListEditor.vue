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
                    {{order.created_at | luxon}}
                </option>
            </b-form-select>
        </div>
        <div class="col-12 form-group">
            <label class="col-12">Выборки</label>
            <b-form-select v-model="selectedOrderPayment">
                <option value="null">-- Выбранные товары --</option>
                <option :key="orderPayments.id"
                        :value="orderPayment"
                        v-for="orderPayment in orderPayments">
                    {{orderPayment.created_at | luxon}}
                </option>
            </b-form-select>
        </div>
        <div class="col-12 form-group">
            <label class="col-12">Товаров выбрано на сумму (в долларах)</label>
            <input class="form-control" v-model="paymentSum" disabled>
        </div>

<!--        <div class="col-12 mb-3">-->
<!--            <button class="btn btn-primary" @click="detailedView = !detailedView">Переключить вид</button>-->
<!--        </div>-->

        <div class="col-12 pb-4" v-show="!detailedView">
            <stored-item-info-table
                    :borderless="borderless"
                    :responsive="responsive"
                    :providedStoredItems="items"
                    :providedSelectedStoredItems="selectedItems"
                    :striped="striped"
                    prevent-item-loading
                    @onItemsSelected="onItemsSelected"
                    ref="storedItemsTable"
                    title="Список товаров">
            </stored-item-info-table>
        </div>

        <div class="col-12 pb-4" v-if="detailedView">
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
                    ref="storedItemsTable"
                    title="Список товаров">
            </stored-items-table-card>
        </div>

        <div class="col-12 form-group">
            <div class="input-group">
                <div class="input-group-prepend">
                    <div class="input-group-text">
                        <input type="checkbox" v-model="isDebtRequested" name="isDebtRequested">
                    </div>
                </div>
                <input type="text" class="form-control"
                       value="Учитывать возможность предоставления долга" disabled>
            </div>
        </div>

        <div class="col-12 text-center">
            <button @click="createPendingPayment" class="btn btn-primary  m-2">Оформить заявку</button>
            <button @click="submit" class="btn btn-secondary m-2">Выдать сразу</button>
        </div>


        <b-modal id="payment-error" title="Ошибка оплаты" ok-only centered ok-title="Закрыть">
            <p class="my-4">{{errorMessage}}</p>
        </b-modal>
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
                selectedOrderPayment: null,
                items: [],
                orderPayments: [],
                selectedItems: [],
                errorMessage: null,
                isDebtRequested: false,
                paymentSum: 0,
                detailedView:false
            }
        },
        methods: {
            clientSelected(client) {
                this.client = client
            },
            onItemsSelected(items) {
                this.selectedItems = items;
                this.calculateTotalPayment();
            },
            calculateTotalPayment() {
                this.paymentSum = Math.round(this.selectedItems.reduce((sum, nextItem) => sum + nextItem.info.billingInfo.pricePerItem, 0) * 100) / 100;
            },
            async createPendingPayment() {
                if(this.selectedItems.length === 0)
                    return;

                tShowSpinner();
                this.$bvModal.hide('payment-error');

                let data = {
                    items: this.selectedItems.map((item) => {
                        return item.id
                    }),
                    isDebtRequested: this.isDebtRequested
                };

                let action = `/deliver/${this.selectedOrder.id}/items/pending-payment`;

                try {
                    const response = await axios.post(action, data);
                    window.location = `/payment/${response.data}/edit`;
                } catch (e) {
                    if (e.response&& e.response.status === 400 || e.response.status === 422) {
                        this.errorMessage = e.response.data.message;
                        this.$bvModal.show('payment-error');
                    }
                }
                tHideSpinner();
            },
            async submit() {
                if(this.selectedItems.length === 0)
                    return;

                tShowSpinner();
                this.$bvModal.hide('payment-error');

                let data = {
                    items: this.selectedItems.map((item) => {
                        return item.id
                    }),
                    isDebtRequested: this.isDebtRequested
                };

                let action = `/deliver/${this.selectedOrder.id}/items`;

                try {
                    const response = await axios.post(action, data);
                    window.location = `/payment/${response.data}`;
                } catch (e) {
                    if (e.response&& e.response.status === 400 || e.response.status === 422) {
                        this.errorMessage = e.response.data.message;
                    } else {
                        this.errorMessage = "Не удалось оформить выдачу товаров. Повтороите попытку после перезагрузки страницы"
                    }
                    this.$bvModal.show('payment-error');
                }
                tHideSpinner();
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
                    const itemsResponse = await getUnpaidOrderItems(this.selectedOrder.id);
                    this.items = itemsResponse.data.map(function (item) {
                        item.info.owner = this.client;
                        return item;
                    }, this);
                    const paymentsResponse = await getOrderPayments(this.selectedOrder.id);
                    this.orderPayments = paymentsResponse.data;
                } catch (e) {
                    this.$root.showErrorMsg(
                        "Ошибка загрузки",
                        "Не удалось получить список товаров"
                    )
                }
                tHideSpinner();
            },
            selectedOrderPayment() {
                let ids = this.selectedOrderPayment.paidItems.map(function (paidItem) {
                        return paidItem.storedItem.id;
                    });

                this.selectedItems = this.items.filter(function (item) {
                        return ids.includes(item.id);
                    });
            }
        }
    }
</script>
