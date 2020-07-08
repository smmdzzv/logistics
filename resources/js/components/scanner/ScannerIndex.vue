<template>
    <div class="container-fluid">
        <div class="row my-4">
            <h2 class="col-12">Работа с товарами</h2>
        </div>
        <div class="row">
            <div class="form-group col-md-4">
                <label>Тип операции</label>
                <select class="form-control form-control-sm" v-model="operation">
                    <option value="deliver">Выдать товары клиенту</option>
                    <option value="load">Загрузить на рейс</option>
                    <option value="store">Принять на склад</option>
                    <option value="transfer">Перевести с рейса на рейс</option>
                </select>
            </div>
            <div class="form-group col-md-8">
                <label>Код товара</label>
                <input type="text"
                       ref="codeInput"
                       class="form-control form-control-sm auto-switch-off"
                       placeholder="Просканируйте штрих код или введите код вручную"
                       v-model="code"
                       autofocus
                       @keyup.enter="fetchStoredItem">
            </div>
        </div>

        <div class="row mt-4">
            <div class="col-12">
                <deliver-stored-items v-if="operation === 'deliver'" :stored-items="items"></deliver-stored-items>

                <load-car v-if="operation === 'load'" :stored-items="items"></load-car>

                <store-items v-if="operation === 'store'" :stored-items="items" :branch="branch"></store-items>
            </div>
        </div>
        <div class="row mt-5">
            <div class="col-12">
                <b-table head-variant="dark" small :items="items" :fields="fields" responsive striped>
                    <template slot='index' slot-scope="data">
                        {{data.index + 1}}
                    </template>

                    <template slot='size' slot-scope="{item}">
                        {{`${item.info.width}x${item.info.height}x${item.info.length}`}}
                    </template>
                </b-table>
            </div>
        </div>
    </div>
</template>

<script>
    import DeliverStoredItems from "./DeliverStoredItems";
    import StoreItems from "./StoreItems";
    import LoadCar from "./LoadCar";

    export default {
        name: "ScannerIndex",
        components: {StoreItems, LoadCar, DeliverStoredItems},
        props: {
            branch: Object
        },
        data() {
            return {
                operation: 'deliver',
                items: [],
                fields: {
                    index: {
                        label: '№'
                    },
                    code: {
                        label: 'Код'
                    },
                    size: {
                        label: 'ШхВхД'
                    },
                    'info.weight': {
                        label: 'Вес'
                    },
                    'info.owner.code': {
                        label: 'Владелец'
                    }
                },
                code: null
            }
        },
        methods: {
            fetchStoredItem() {
                axios.get(`/stored-items/${this.code}`)
                    .then(response => {
                        if (!this.items.find(i => i.id === response.data.id)) {
                            response.data.info.width = response.data.info.width.toFixed(3);
                            response.data.info.height = response.data.info.height.toFixed(3);
                            response.data.info.length = response.data.info.length.toFixed(3);
                            response.data.info.weight = response.data.info.weight.toFixed(3);
                            this.items.unshift(response.data)
                        }
                    });
                this.code = null;
            }
        }
    }
</script>

<style scoped>

</style>
