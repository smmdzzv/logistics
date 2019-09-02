<template>
    <div>
        <b-modal id="addItemModal"
                 ref="modal"
                 size="xl"
                 no-close-on-esc
                 title="Добавить новый товар"
                 ok-title="Добавить"
                 cancel-title="Отменить"
                 @close="clearForm"
                 @cancel="clearForm"
                 @ok.prevent="onAdded">
            <form id="addItemForm">
                <div class="d-block">
                    <div class="container pt-4">
                        <div class="row">
                            <div class="form-group col-md-3">
                                <label for="width" class="col-form-label text-md-right">Ширина</label>
                                <input v-model.number="storedItem.width"
                                       name="width"
                                       @blur="$v.storedItem.width.$touch()"
                                       maxlength="6"
                                       class="form-control"
                                       id="width"
                                       placeholder="в метрах"
                                       required>
                                <b-popover
                                    :show.sync="$v.storedItem.width.$error"
                                    variant="danger"
                                    target="width"
                                    placement="bottom"
                                    content="Введите ширину в метрах"
                                    triggers="null"/>
                            </div>

                            <div class="form-group col-md-3">
                                <label for="height" class="col-form-label text-md-right">Высота</label>
                                <input v-model="storedItem.height"
                                       name="height"
                                       @blur="$v.storedItem.height.$touch()"
                                       maxlength="6"
                                       class="form-control"
                                       id="height"
                                       placeholder="в метрах" required>
                                <b-popover
                                    :show.sync="$v.storedItem.height.$error"
                                    variant="danger"
                                    target="height"
                                    placement="bottom"
                                    content="Введите высоту в метрах"
                                    triggers="null"/>
                            </div>


                            <div class="form-group col-md-3">
                                <label for="length" class="col-form-label text-md-right">Длина</label>
                                <input v-model="storedItem.length"
                                       name="length"
                                       @blur="$v.storedItem.length.$touch()"
                                       maxlength="6"
                                       class="form-control"
                                       id="length"
                                       placeholder="в метрах" required>
                                <b-popover
                                    :show.sync="$v.storedItem.length.$error"
                                    variant="danger"
                                    target="length"
                                    placement="bottom"
                                    content="Введите длину в метрах"
                                    triggers="null"/>
                            </div>

                            <div class="form-group col-md-3">
                                <label for="weight" class="col-form-label text-md-right">Вес</label>
                                <input v-model="storedItem.weight"
                                       name="weight"
                                       @blur="$v.storedItem.weight.$touch()"
                                       maxlength="6"
                                       class="form-control"
                                       id="weight"
                                       placeholder="в кг"
                                       required>
                                <b-popover
                                    :show.sync="$v.storedItem.weight.$error"
                                    variant="danger"
                                    target="weight"
                                    placement="bottom"
                                    content="Введите вес в килограммах"
                                    triggers="null"/>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-md-8">
                                <suggestions-input id="item"
                                                   title="Наимнование товара"
                                                   placeholder="Введите название товара"
                                                   keyPropertyName="id"
                                                   displayPropertyName="name"
                                                   :onItemSearchInputChange="onItemSearchInputChange"
                                                   :onSelected="onItemSelected"
                                                   v-bind:options="filteredItems"
                                                   required/>
                                <b-popover
                                    :show.sync="$v.storedItem.item.$error"
                                    variant="danger"
                                    target="item"
                                    placement="bottom"
                                    content="Найдите товар по имени"
                                    triggers="null"/>
                            </div>
                            <div class="form-group col-md-2">
                                <label for="count" class="col-form-label text-md-right">Количество</label>
                                <input class="form-control"
                                       v-model="storedItem.count"
                                       @blur="$v.storedItem.count.$touch()"
                                       id="count"
                                       maxlength="4"
                                       name="count"
                                       placeholder="в шт" required>
                                <b-popover
                                    :show.sync="$v.storedItem.count.$error"
                                    variant="danger"
                                    target="count"
                                    placement="bottom"
                                    content="Определите количество товаров"
                                    triggers="null"/>
                            </div>

<!--                            <div class="form-group col-md-2">-->
<!--                                <label for="branch" class="col-form-label text-md-right">Филиал</label>-->
<!--                                <select id="branch"-->
<!--                                        class="form-control custom-select" required>-->
<!--                                    <option v-model="branches"-->
<!--                                            v-for="branch in branches">{{branch.name}}-->
<!--                                    </option>-->
<!--                                </select>-->
<!--                            </div>-->
                            <div class="form-group col-md-2">
                                <label for="branch" class="col-form-label text-md-right">Филиал</label>
                                <input class="form-control" v-model="branch.name" name="branch" id="branch" disabled>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </b-modal>
    </div>

</template>

<script>
    import {required, maxLength, decimal, integer, not} from 'vuelidate/lib/validators';

    export default {
        name: "StoredItemBox",
        props:{
            branch: null,
            onStoredItemAdded: {
                type: Function,
                required: true
            }
        },
        mounted() {
            // axios.get(`/branches`)
            //     .then(result => {
            //         if (result) {
            //             this.branches = result.data;
            //         }
            //     });
            axios.get('/items')
                .then(result => {
                    if (result) {
                        this.items = result.data;
                    }
                });
        },
        data() {
            return {
                items: [],
                filteredItems: [],
                storedItem: {
                    width: null,
                    height: null,
                    length: null,
                    weight: null,
                    count: null,
                    branch: this.$props.branch,
                    item: null
                }
            }
        },
        methods: {
            onItemSearchInputChange(query) {
                if (query === "")
                    return this.filteredItems = [];
                this.filteredItems = this.items.filter(value => {
                    return value.name.toLowerCase().includes(query.toLowerCase())
                });
            },
            onItemSelected(item){
                this.storedItem.item = item;
                console.log('on item selected/ item-.'+ this.storedItem.item.name)
            },
            clearForm(e){
                console.log('onclear')
                if(e) e.preventDefault();
                this.storedItem.weight = '';
                this.storedItem.height = '';
                this.storedItem.length = '';
                this.storedItem.width = '';
                this.storedItem.count = '';
                this.storedItem.item = null;
                this.filteredItems = [];
                this.$nextTick(()=>{
                    this.$v.$reset();
                    this.$refs.modal.hide()
                })
            },
            onAdded(e){
                if(e) e.preventDefault();

                if(this.$v.$invalid)
                    this.$v.$touch();
                else{
                    let _item = $.extend(true, {}, this.storedItem);

                    this.onStoredItemAdded(_item);
                    this.clearForm(null);
                }
            }
        },
        components: {
            SuggestionsInput: require('./SuggestionInput').default
        },
        validations:{
            storedItem:{
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
                item:{
                        required
                }
            }
        }
    }
</script>

<style scoped>

</style>
