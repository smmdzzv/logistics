<template>
    <div class="container">
        <div class="form-group row dropdown">
            <label for="query" class="col-form-label text-md-right" v-text="title"></label>
            <input id="query"
                   class="form-control col-md-12"
                   data-toggle="dropdown"
                   v-model="query"
                   @click.stop.prevent.capture
                   v-on:input="onInputChanged"
                   v-on:focus="showOptions"
                   @keydown="onKeyDown"
                   type="text"
                   name="query"
                   autocomplete="query" autofocus
                   v-bind:placeholder="placeholder">
            <div id="optionsMenu"
                 class="dropdown-menu"
                 :class="{show: options.length > 0}">
                <div v-bind:property="option"
                     class="dropdown-item"
                     @click.stop.prevent="onOptionSelected(option)"
                     v-for="(option, index) in options"
                     :class="{active: index === activeOptionIndex}">
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
            console.log("mounted " + this.options);
        },
        data() {
            return {
                query: '',
                activeOptionIndex: 0
            }
        },
        methods: {
            onInputChanged(){
                this.$props.onItemSearchInputChange(this.query);
                this.activeOptionIndex = 0
            },
            onOptionSelected(option){
                this.hideOptions();
                if(option === null){
                  option =  this.$props.options.find((item, index) => {
                        return index === this.activeOptionIndex
                    })
                }
                if(option === 'undefined')
                    option = this.$props.options[0];

                this.activeOptionIndex = 0;

                this.query = option[this.$props.displayPropertyName];
                this.$props.onSelected(option);
                this.$props.onItemSearchInputChange(this.query);
                $('#query').blur();
            },
            onKeyDown(e){
                    switch (e.keyCode) {
                        case 40:
                            e.preventDefault();
                            this.setActiveItem('down');
                            break;
                        case 38:
                            e.preventDefault();
                            this.setActiveItem('up');
                            break;
                        case 13: //enter
                            e.preventDefault();
                            this.onOptionSelected(null)
                            break;
                        default:
                            return true
                    }
            },

            setActiveItem(direction){
                let newActiveIndex = this.activeOptionIndex;
                if(direction === 'down') newActiveIndex++;
                else newActiveIndex--;

                if(newActiveIndex < 0)
                    newActiveIndex = this.$props.options.length - 1;
                if(newActiveIndex >= this.$props.options.length)
                    newActiveIndex = 0;
                this.activeOptionIndex = newActiveIndex
            },

            //Options view state control
            toggleDropdown(){
                if(this.$props.options.length > 0){
                    //in case of dropdown('show')  dropdown appears at wrong position
                    return $("#query").dropdown('toggle');
                }
                return $('#optionsMenu').dropdown('hide');
            },
            showOptions(){
                if(this.$props.options.length > 0)
                    return $('#optionsMenu').dropdown('show');
                return this.hideOptions()
            },
            hideOptions(){
                $('#optionsMenu').dropdown('hide');
            }
        }
    }
</script>

<style>

</style>
