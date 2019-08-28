<template>
    <div class="container">
        <div class="form-group row dropdown">
            <label for="query" class="col-form-label text-md-right" v-text="title"></label>
            <input id="query"
                   v-on:input="onItemSearchInputChange(query)"
                   class="form-control col-md-12"
                   data-toggle="dropdown"
                   v-model="query"
                   @click.stop.prevent.capture="toggleDropdown"
                   type="text"
                   name="query"
                   autocomplete="query" autofocus
                   v-bind:placeholder="placeholder">
            <div id="optionsMenu" class="dropdown-menu">
                <div v-bind:property="option"
                     v-on:click.stop.prevent
                     class="dropdown-item"
                     @click="onOptionSelected(option[keyPropertyName])"
                     v-for="(option, index) in options"
                     :key="option[keyPropertyName]">
                    {{option[displayPropertyName]}}
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        props: {
            options: Array,
            title: String,
            placeholder: String,
            displayPropertyName: String,
            keyPropertyName: String,
            onItemSearchInputChange: {
                type: Function,
                required: true
            },
            onSelected:{
                type: Function,
                required: true
            }
        },
        mounted() {
            console.log("mounted " + this.options)
        },
        data() {
            return {
                query: ''
            }
        },
        methods: {
            onOptionSelected(option){
                this.toggleDropdown();
                this.$props.onSelected(option)
            },
            toggleDropdown(){
                if(this.$props.options.length > 0){
                    //in case of dropdown('show')  dropdown appears at wrong position
                    return $("#query").dropdown('toggle');
                }
                return $('#optionsMenu').dropdown('hide');
            }
        }
    }
</script>

<style>

</style>
