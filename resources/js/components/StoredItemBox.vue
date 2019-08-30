<template>
 <div class="container pt-4">
     <div class="row">
         <div class="form-group col-md-3">
             <label for="width" class="col-form-label text-md-right">Ширина</label>
             <input class="form-control" id="width" placeholder="в метрах">
         </div>

         <div class="form-group col-md-3">
             <label for="height" class="col-form-label text-md-right">Высота</label>
             <input class="form-control" id="height" placeholder="в метрах">
         </div>


         <div class="form-group col-md-3">
             <label for="length" class="col-form-label text-md-right">Длина</label>
             <input class="form-control" id="length" placeholder="в метрах">
         </div>

         <div class="form-group col-md-3">
             <label for="weight" class="col-form-label text-md-right">Вес</label>
             <input class="form-control" id="weight" placeholder="в кг">
         </div>
     </div>
     <div class="form-row">
         <div class="form-group col-md-8">
<!--             <label for="item" class="col-form-label text-md-right"></label>-->
<!--             <input id="item" class="form-control">-->
             <suggestions-input title="Наимнование товара"
                                placeholder="Введите название товара"
                                keyPropertyName = "id"
                                displayPropertyName = "name"
                                :onItemSearchInputChange="onItemSearchInputChange"
                                :onSelected="onItemSelected"
                                v-bind:options="filteredItems"/>
         </div>
         <div class="form-group col-md-2">
             <label for="count" class="col-form-label text-md-right">Количество</label>
             <input class="form-control" id="count" placeholder="в шт">
         </div>

         <div class="form-group col-md-2">
             <label for="branch" class="col-form-label text-md-right">Филиал</label>
             <select id="branch"
                     class="form-control custom-select">
                 <option  v-model="branches"
                          v-for="branch in branches">{{branch.name}}</option>
             </select>
         </div>
     </div>
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
                selectedItem: null
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
                this.selectedItem = item;
            }
        },
        components:{
            SuggestionsInput: require('./SuggestionInput').default
        }
    }
</script>

<style scoped>

</style>
