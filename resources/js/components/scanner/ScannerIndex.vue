<template>
    <div class="container-fluid">
        <div class="row my-4">
            <h2 class="col-12">Работа с товарами</h2>
        </div>
        <div class="row">
            <div class="form-group col-md-4">
                <label>Тип операции</label>
                <select class="form-control" v-model="operation">
                    <option value="deliver">Выдать товары клиенту</option>
                    <option value="store">Принять на склад</option>
                    <option value="load">Загрузить на рейс</option>
                </select>
            </div>
            <div class="form-group col-md-8">
                <label>Код товара</label>
                <input type="text"
                       ref="codeInput"
                       class="form-control auto-switch-off"
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
                <div class="d-flex">
                    <b-button class="ml-auto mb-3" @click="showModal">Сохранить выборку</b-button>
                </div>
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

        <b-modal
            id="selection-name-modal"
            ref="modal"
            title="Сохранить выборку"
            @show="resetModal"
            @hidden="resetModal"
            @ok="storeSelection"
            ok-title="Сохранить"
            cancel-title="Отмена"
            centered
        >
            <b-form-group
                :state="nameState"
                label="Имя"
                label-for="name-input"
                invalid-feedback="Введите название выборки"
            >
                <b-form-input
                    id="name-input"
                    v-model="name"
                    :state="nameState"
                    required
                ></b-form-input>
            </b-form-group>
        </b-modal>
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
                fields: [
                    {
                        key: 'index',
                        label: '№'
                    },
                    {
                        key: 'code',
                        label: 'Код'
                    },
                    {
                        key: 'size',
                        label: 'ШхВхД'
                    },
                    {
                        key: 'info.weight',
                        label: 'Вес'
                    },
                    {
                        key: 'info.owner.code',
                        label: 'Владелец'
                    }
                ],
                code: null,
                name: null
            }
        },
        methods: {
            fetchStoredItem() {
                axios.get(`/stored-items/available/${this.code}`)
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
            },
            showModal() {
                if (this.items.length > 0)
                    this.$bvModal.show('selection-name-modal');
                else this.$bvModal.msgBoxOk('Для создания выборки необходимо выбрать хотя бы 1 товар', {
                    okTitle: 'Закрыть',
                    title: 'Ошибка сохранения',
                    centered: true
                })
            },
            storeSelection() {
                axios.post('/items-selection/', {
                    storedItems: this.items.map(i => i.id),
                    name: this.name
                })
            },
            resetModal() {
                this.name = null
            }
        },
        computed: {
            nameState() {
                return Boolean(this.name);
            }
        }
    }
</script>
