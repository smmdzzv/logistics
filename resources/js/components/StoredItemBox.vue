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
             <label for="item" class="col-form-label text-md-right">Наимнование товара</label>
             <input id="item" class="form-control">
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
     <suggestions-input title="Поисковик"
                        placeholder="Введите название товара"
                        keyPropertyName = "id"
                        displayPropertyName = "name"
                        :onItemSearchInputChange="onItemSearchInputChange"
                        :onSelected="onItemSelected"
                        v-bind:options="searchOptions"></suggestions-input>
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
        },
        data(){
            return{
                branches:[],
                searchOptions:[]
            }
        },
        methods:{
            onItemSearchInputChange(query){
                this.searchOptions = this.branches.filter(value => {
                    return value.name.startsWith(query)
                })
                console.log(this.searchOptions)
            },
            onItemSelected(item){
                console.log(item)
            }
        },
        components:{
            SuggestionsInput: require('./SuggestionInput').default
        }
    }
</script>

<style scoped>

</style>
