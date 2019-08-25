<template>
 <div class="container pt-4">
     <div class="form-group row">
         <label for="width" class="col-form-label col-md-3 text-md-right">Ширина</label>
         <div class="col-md-2 pb-2">
             <input class="form-control" id="width">
         </div>

         <label for="height" class="col-form-label col-md-3 text-md-right">Высота</label>
         <div class="col-md-2">
             <input class="form-control" id="height">
         </div>

         <label for="length" class="col-form-label col-md-3 text-md-right">Длина</label>
         <div class="col-md-2">
             <input class="form-control" id="length">
         </div>

         <label for="weight" class="col-form-label col-md-3 text-md-right">Вес</label>
         <div class="col-md-2">
             <input class="form-control" id="weight">
         </div>
     </div>
     <div class="form-group row">
         <label for="count" class="col-form-label col-md-3 text-md-right">Количество</label>
         <div class="col-md-2">
             <input class="form-control" id="count">
         </div>

         <label for="branch" class="col-form-label col-md-3 text-md-right">Филиал</label>
         <div class="col-md-2">
             <select class="form-control custom-select" id="branch">
                 <option class="sele">ИВУ</option>
             </select>
         </div>
     </div>
 </div>
</template>

<script>
    export default {
        name: "StoredItemBox"
    }
</script>

<style scoped>

</style>
