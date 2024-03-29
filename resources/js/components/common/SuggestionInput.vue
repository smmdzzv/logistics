<template>
    <div class="dropdown">
        <input
               @keyup="onKeyUp"
               autofocus
               class="form-control col-md-12"
               data-toggle="dropdown"
               id="query"
               name="query"
               type="text"
               v-bind:placeholder="placeholder"
               v-model="query"
               :class="{'is-invalid': isInvalid, 'form-control-sm': sm}"
               v-on:blur="hideDropdown"
               v-on:focus="showDropdown"
               v-on:input="onInputChanged">
        <div :class="{show: showOptions && options.length > 0}"
             class="dropdown-menu"
             id="optionsMenu">
            <div :class="{active: index === activeOptionIndex}"
                 @click.stop.prevent="onOptionSelected(option)"
                 class="dropdown-item"
                 v-bind:property="option"
                 v-for="(option, index) in options">
                {{option[displayPropertyName]}}
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        props: {
            options: {
                type: Array,
                required: true
            },
            placeholder: String,
            displayPropertyName: String,
            keyPropertyName: {
                type: String,
                default: 'id'
            },
            isInvalid: {
                required: false,
                default: false
            },
            onItemSearchInputChange: {
                type: Function,
                required: true
            },
            onSelected: {
                type: Function,
                required: true
            },
            initQuery: {
                type: String,
                required: false
            },
            sm:{
                type:Boolean
            }
        },
        //TODO v-model binding
        created(){
            if(this.initQuery){
                this.query = this.initQuery;
            }
        },
        data() {
            return {
                query: '',
                activeOptionIndex: 0,
                showOptions: false
            }
        },
        watch: {
            initQuery() {
                this.query = this.initQuery;
            }
        },
        methods: {
            onInputChanged() {
                this.onItemSearchInputChange(this.query);
                this.activeOptionIndex = 0
            },
            onOptionSelected(option) {
                if (!option) {
                    option = this.options.find((item, index) => {
                        return index === this.activeOptionIndex
                    })
                }
                if (option === 'undefined')
                    option = this.options[0];

                this.activeOptionIndex = 0;
                if(option)
                    this.query = option[this.displayPropertyName];
                this.onSelected(option);
                this.onItemSearchInputChange(this.query);
                $('#query').blur();
            },
            onKeyUp(e) {
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
                        this.onOptionSelected(null);
                        break;
                    default:
                        return true
                }
            },

            setActiveItem(direction) {
                let newActiveIndex = this.activeOptionIndex;
                if (direction === 'down') newActiveIndex++;
                else newActiveIndex--;

                if (newActiveIndex < 0)
                    newActiveIndex = this.options.length - 1;
                if (newActiveIndex >= this.options.length)
                    newActiveIndex = 0;
                this.activeOptionIndex = newActiveIndex
            },
            showDropdown() {
                this.showOptions = true
            },
            hideDropdown() {
                let self = this;
                setTimeout(function () {
                    self.showOptions = false;
                }, 150);
            }
        }
    }
</script>

<style>

</style>
