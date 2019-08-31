<template>
    <div>
        <b-modal id="addItemModal" size="xl" hide-footer title="Добавить новый товар">
            <form id="addItemForm">
              <div class="d-block">
                <div class="container pt-4">
                    <div class="row">
                        <div class="form-group col-md-3">
                            <label for="width" class="col-form-label text-md-right">Ширина</label>
                            <input  v-model="storedItem.width" class="form-control" id="width" placeholder="в метрах" required>
                        </div>

                        <div class="form-group col-md-3">
                            <label for="height" class="col-form-label text-md-right">Высота</label>
                            <input  v-model="storedItem.height" class="form-control" id="height" placeholder="в метрах" required>
                        </div>


                        <div class="form-group col-md-3">
                            <label for="length" class="col-form-label text-md-right">Длина</label>
                            <input  v-model="storedItem.length" class="form-control" id="length" placeholder="в метрах" required>
                        </div>

                        <div class="form-group col-md-3">
                            <label for="weight" class="col-form-label text-md-right">Вес</label>
                            <input v-model="storedItem.weight" class="form-control" id="weight" placeholder="в кг" required>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-8">
                            <suggestions-input title="Наимнование товара"
                                               placeholder="Введите название товара"
                                               keyPropertyName = "id"
                                               displayPropertyName = "name"
                                               :onItemSearchInputChange="onItemSearchInputChange"
                                               :onSelected="onItemSelected"
                                               v-bind:options="filteredItems"
                                               required/>
                        </div>
                        <div class="form-group col-md-2">
                            <label for="count" class="col-form-label text-md-right">Количество</label>
                            <input class="form-control" id="count" placeholder="в шт" required>
                        </div>

                        <div class="form-group col-md-2">
                            <label for="branch" class="col-form-label text-md-right">Филиал</label>
                            <select id="branch"
                                    class="form-control custom-select" required>
                                <option  v-model="branches"
                                         v-for="branch in branches">{{branch.name}}</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
            </form>
        </b-modal>
    </div>

</template>

<script>
    export default {
        name: "StoredItemBox",
        mounted(){
            axios.get(`/branches`)
                .then(result => {
                    if(result){
                        this.branches = result.data;
                    }
                });
            axios.get('/items')
                .then(result =>{
                    if(result){
                        this.items = result.data;
                    }
                })
        },
        data(){
            return{
                branches:[],
                items:[],
                filteredItems:[],
                storedItem: {
                    width: '',
                    height: '',
                    length: '',
                    weight: '',
                    count: '',
                    branch: '',
                    item: Object
                }
            }
        },
        methods:{
            onItemSearchInputChange(query){
                if(query === "")
                    return this.filteredItems = [];
                this.filteredItems = this.items.filter(value => {
                    return value.name.toLowerCase().includes(query.toLowerCase())
                });
            },
            onItemSelected(item){
                this.storedItem.item = item;
            }
        },
        components:{
            SuggestionsInput: require('./SuggestionInput').default
        }
    }
</script>

<style scoped>

</style>
