<template>
    <div>
        <form @submit.prevent id="addItemForm">
            <div class="container">
                <div class="row">
                    <div class="form-group col-md-4">
                        <label class="col-form-label text-md-right" for="shop">Магазин</label>
                        <input class="form-control"
                               id="shop"
                               name="shop"
                               placeholder="код или название магазина"
                               required v-model="storedItem.shop">
                        <!--                        <select class="form-control custom-select"-->
                        <!--                                id="shop"-->
                        <!--                                required v-model="storedItem.shop">-->
                        <!--                            <option :value="null" disabled>&#45;&#45; Выберите магазин &#45;&#45;</option>-->
                        <!--                            <option :value="shop" v-for="shop in shops">{{shop.code}} {{shop.name}}</option>-->
                        <!--                        </select>-->
                    </div>

                    <div class="form-group col-md-5">
                        <label class="col-form-label text-md-right" for="item">Наименование товара</label>
                        <suggestions-input :onItemSearchInputChange="onItemSearchInputChange"
                                           :onSelected="onItemSelected"
                                           :initQuery="itemInitQuery"
                                           displayPropertyName="name"
                                           id="item"
                                           keyPropertyName="id"
                                           placeholder="Введите название товара"
                                           ref="suggestionInput"
                                           required
                                           v-bind:options="filteredItems"/>
                        <b-popover
                            :show.sync="$v.storedItem.item.$error"
                            content="Найдите товар по имени"
                            placement="bottom"
                            target="item"
                            triggers="null"
                            variant="danger"/>
                    </div>


                    <div class="form-group col-md-3">
                        <label class="col-form-label text-md-right" for="weight">Вес</label>
                        <input @blur="$v.storedItem.weight.$touch()"
                               class="form-control"
                               id="weight"
                               type="number"
                               step="0.01"
                               name="weight"
                               placeholder="в кг"
                               required
                               autofocus
                               v-model.number="storedItem.weight">
                        <b-popover
                            :show.sync="$v.storedItem.weight.$error"
                            content="Введите вес в килограммах"
                            placement="bottom"
                            target="weight"
                            triggers="null"
                            variant="danger"/>
                    </div>
                </div>
                <div class="row">
                    <div class="form-group col-md-3">
                        <label class="col-form-label text-md-right" for="width">Ширина</label>
                        <input @blur="$v.storedItem.width.$touch()"
                               class="form-control"
                               id="width"
                               type="number"
                               step="0.01"
                               name="width"
                               placeholder="в метрах"
                               required
                               v-model.number="storedItem.width">
                        <b-popover
                            :show.sync="$v.storedItem.width.$error"
                            content="Введите ширину в метрах"
                            placement="bottom"
                            target="width"
                            triggers="null"
                            variant="danger"/>
                    </div>

                    <div class="form-group col-md-3">
                        <label class="col-form-label text-md-right" for="height">Высота</label>
                        <input @blur="$v.storedItem.height.$touch()"
                               class="form-control"
                               id="height"
                               type="number"
                               step="0.01"
                               name="height"
                               placeholder="в метрах"
                               required v-model.number="storedItem.height">
                        <b-popover
                            :show.sync="$v.storedItem.height.$error"
                            content="Введите высоту в метрах"
                            placement="bottom"
                            target="height"
                            triggers="null"
                            variant="danger"/>
                    </div>

                    <div class="form-group col-md-3">
                        <label class="col-form-label text-md-right" for="length">Длина</label>
                        <input @blur="$v.storedItem.length.$touch()"
                               class="form-control"
                               id="length"
                               type="number"
                               step="0.01"
                               name="length"
                               placeholder="в метрах"
                               required v-model.number="storedItem.length">
                        <b-popover
                            :show.sync="$v.storedItem.length.$error"
                            content="Введите длину в метрах"
                            placement="bottom"
                            target="length"
                            triggers="null"
                            variant="danger"/>
                    </div>

                    <div class="form-group col-md-3">
                        <label class="col-form-label text-md-right" for="count">Кол-во мест</label>
                        <input @blur="$v.storedItem.count.$touch()"
                               :disabled="storedItem.id"
                               class="form-control"
                               id="count"
                               maxlength="4"
                               name="count"
                               placeholder="в ед. товара"
                               required v-model.number="storedItem.count">
                        <b-popover
                            :show.sync="$v.storedItem.count.$error"
                            content="Определите количество товаров"
                            placement="bottom"
                            target="count"
                            triggers="null"
                            variant="danger"/>
                    </div>
                </div>

                <div class="row">


                    <!--                    <div class="form-group col-md-4" id="tariff-wrapper">-->
                    <!--                        <label class="col-form-label text-md-right" for="tariff">Тариф</label>-->
                    <!--                        <input class="form-control" type="text" id="tariff" :value="tariff.name" disabled>-->
                    <!--                        <b-tooltip target="tariff-wrapper" triggers="hover">-->
                    <!--                            Определенно в свойствах наименования-->
                    <!--                        </b-tooltip>-->
                    <!--                    </div>-->

                    <div class="form-group col-md-4">
                        <label class="col-form-label text-md-right" for="tariff">Тариф</label>
                        <select class="form-control custom-select"
                                name="tariff"
                                id="tariff"
                                required v-model="storedItem.tariff">
                            <option value="null" disabled>-- Выберите тариф --</option>
                            <option :value="tariff"
                                    v-for="tariff in tariffs"
                            >{{tariff.name}}
                            </option>
                        </select>
                        <b-popover
                            :show.sync="$v.storedItem.tariff.$error"
                            content="Выберите тариф из списка"
                            placement="bottom"
                            target="tariff"
                            triggers="null"
                            variant="danger"/>
                    </div>

                    <div class="form-group col-md-4">
                        <label class="col-form-label text-md-right" for="customs-code">Таможенный код</label>
                        <select class="form-control custom-select"
                                id="customs-code"
                                v-model="storedItem.customsCode"
                                required>
                            <option value="null" disabled>-- Выберите таможенный код --</option>
                            <option :value="customsCode"
                                    v-for="customsCode in customsCodes"
                            >{{customsCode.code}} - {{customsCode.name}}
                            </option>
                        </select>
                        <b-popover
                            :show.sync="$v.storedItem.customsCode.$error"
                            content="Выберите таможенный код из списка"
                            placement="bottom"
                            target="customs-code"
                            triggers="null"
                            variant="danger"/>
                    </div>

                    <div class="form-group col-md-4">
                        <label class="col-form-label text-md-right" for="branch">Филиал</label>
                        <input class="form-control" disabled id="branch" name="branch" v-model="branch.name">
                    </div>
                </div>

                <div class="col-12 text-center pt-2">
                    <button @click="onAdded" class="btn btn-primary">Добавить</button>
                </div>
            </div>
        </form>

        <!--         Custom price modal-->
        <b-modal
            @hidden="resetModal"
            @ok="handleOk"
            @show="resetModal"
            ok-title="Сохранить"
            cancel-title="Отменить"
            id="customPriceModal"
            ref="customPriceModal"
            title="Введите ручную цену за единицу товара"
        >
            <form @submit.stop.prevent="handleSubmit" ref="customPriceForm">
                <b-form-group
                    :state="customPriceState"
                    invalid-feedback="Необходимо ввести ручную стоимость товара"
                    label="Ручная стоимость"
                    label-for="customPrice"
                >
                    <b-form-input
                        id="customPrice"
                        required
                        step="0.01"
                        type="number"
                        v-model="customPrice"
                    ></b-form-input>
                </b-form-group>
            </form>
        </b-modal>
    </div>
</template>

<script>
    import {required, maxLength, decimal, integer} from 'vuelidate/lib/validators';

    export default {
        name: "StoredItemBox",
        props: {
            branch: {
                type: Object,
                required: false,
                default: function () {
                    return {name: ''}
                }
            },
            tariffs: Array,
            providedStoredItemInfo: Object,
            onStoredItemAdded: {
                type: Function,
                required: true
            }
        },
        mounted() {
            this.getItems();
        },
        data() {
            return {
                items: [],
                filteredItems: [],
                customsCodes: [],
                itemInitQuery: '',
                storedItem: {
                    id: null,
                    width: null,
                    height: null,
                    length: null,
                    weight: null,
                    count: null,
                    branch: this.branch,
                    item: null,
                    tariff: null,
                    // placeCount: null,
                    customsCode: null,
                    billingInfo: {
                        tariffPricing: null
                    },
                    shop: null
                },
                // tariff: {name: null},
                customPrice: 0,
                customPriceState: null
            }
        },
        methods: {
            async getItems() {
                try {
                    const response = await axios.get('/items/all/eager');
                    this.items = response.data;
                } catch (e) {
                    this.$root.showErrorMsg(
                        'Ошибка загрузки',
                        'Не удалось загрузить список наименований. Повторите попытку после перезагрузки страницы'
                    )
                }
            },
            async getPricing() {
                //if stored item is being edited pricing should be the same
                if (this.storedItem.billingInfo.tariffPricing
                    && this.providedStoredItemInfo
                    && this.storedItem.item.id === this.providedStoredItemInfo.item.id
                    && this.storedItem.tariff.id === this.providedStoredItemInfo.tariff.id)
                    return;
                tShowSpinner();
                try {
                    let action = `tariff/${this.storedItem.tariff.id}/pricing`;
                    const response = await axios.get(action);
                    this.storedItem.billingInfo.tariffPricing = response.data;
                } catch (e) {
                    this.$root.showErrorMsg(
                        'Ошибка загрузки',
                        'Не удалось загрузить расценки для выбранного тарифа. Убедитесь, что расценки заданы в системе'
                    )
                }
                tHideSpinner();
            },
            onItemSearchInputChange(query) {
                if (query === "")
                    return this.filteredItems = [];
                this.filteredItems = this.items.filter(value => {
                    return value.name.toLowerCase().includes(query.toLowerCase())
                });
            },
            onItemSelected(item) {
                this.storedItem.item = item;
                // this.tariff = this.tariffs.find(x => x.id === item.tariffId);
                if (item)
                    this.customsCodes = item.codes;
            },
            clearForm(e) {
                if (e) e.preventDefault();
                this.itemInitQuery = '';
                this.storedItem.weight = '';
                this.storedItem.id = null;
                this.storedItem.height = '';
                this.storedItem.length = '';
                this.storedItem.width = '';
                this.storedItem.count = '';
                // this.storedItem.placeCount = '';
                this.storedItem.item = null;
                this.filteredItems = [];
                this.storedItem.price = null;
                this.storedItem.customsCode = null;
                this.storedItem.shop = null;
                this.storedItem.tariff = null;
                this.customsCodes = [];
                this.$refs.suggestionInput.query = '';
                this.$nextTick(() => {
                    this.$v.$reset();
                    // this.$refs.modal.hide()
                })
            },
            async onAdded(e) {
                if (e) e.preventDefault();

                if (this.$v.$invalid)
                    this.$v.$touch();
                else {
                    //TODO refactor
                    await this.getPricing();
                    if (this.storedItem.item.onlyCustomPrice)
                        this.showModal();
                    else
                        this.submit();
                }
            },
            submit() {
                //Copy data to new variable
                // let stored = $.extend(true, {}, this.storedItem);
                let stored = JSON.parse(JSON.stringify(this.storedItem));
                this.clearForm(null);
                this.onStoredItemAdded(stored);
            },

            //Custom Price
            showModal() {
                this.$bvModal.show('customPriceModal');
            },
            checkFormValidity() {
                const valid = this.$refs.customPriceForm.checkValidity();
                this.customPriceState = valid ? 'valid' : 'invalid';
                return valid
            },

            resetModal() {
                this.customPrice = 0;
                this.customPriceState = null;
            },

            //Custom Price Modal
            handleOk(bvModalEvt) {
                // Prevent modal from closing
                bvModalEvt.preventDefault();
                // Trigger submit handler
                this.handleSubmit()
            },

            handleSubmit() {
                // Exit when the form isn't valid
                if (!this.checkFormValidity()) {
                    return
                }

                this.storedItem.customPrice = parseFloat(this.customPrice) * parseFloat(this.storedItem.count);

                // Hide the modal manually
                this.$nextTick(() => {
                    this.$refs.customPriceModal.hide();
                    this.submit()
                })
            }

        },
        watch: {
            providedStoredItemInfo() {
                this.onItemSelected(this.providedStoredItemInfo.item);
                //this is needed, because attached customsCode could differ as an object from options (customsCodes)
                // this.storedItem.customsCode = this.customsCodes.find(function (el) {
                //     if (el.id === this.providedStoredItemInfo.customsCode.id)
                //         return el;
                // }, this);

                // this.providedStoredItemInfo.shop = this.shops.find(function (el) {
                //     if(el.id === this.providedStoredItemInfo.shop.id)
                //         return el;
                // }, this);
                this.storedItem = $.extend(true, {}, this.providedStoredItemInfo);
                this.storedItem.customsCode = this.customsCodes.find(function (el) {
                    if (el.id === this.providedStoredItemInfo.customsCode.id)
                        return el;
                }, this);
                this.itemInitQuery = this.providedStoredItemInfo.item.name;
            }
        },
        components: {
            SuggestionsInput: require('../common/SuggestionInput').default
        },
        validations: {
            storedItem: {
                width: {
                    required,
                    decimal,
                    maxLength: maxLength(6)
                },
                height: {
                    required,
                    decimal,
                    maxLength: maxLength(6)
                },
                length: {
                    required,
                    decimal,
                    maxLength: maxLength(6)
                },
                weight: {
                    required,
                    decimal,
                    maxLength: maxLength(6)
                },
                count: {
                    required,
                    integer,
                    maxLength: maxLength(6)
                },
                item: {
                    required
                },
                // placeCount: {
                //     required,
                //     integer
                // },
                customsCode: {
                    required
                },
                tariff: {
                    required
                }
            }
        }
    }
</script>

<style scoped>

</style>
