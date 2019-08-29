<template>
    <div class="v-suggestions ">
        <input type="text"
               :class="extendedOptions.inputClass"
               v-bind="$attrs"
               v-on:keydown="onKeyDown"
               v-on:blur="hideItems"
               v-on:focus="showItems = true"
               v-model="query"
               :autocomplete="Math.random()"
               :placeholder="extendedOptions.placeholder">
        <div class="suggestions">
            <ul class="items" v-show="items.length > 0 && showItems === true">
                <li class="item"
                    :key="index"
                    v-for="(item, index) in items"
                    @click.prevent="selectItem(index)"
                    v-bind:class="{ 'is-active': index === activeItemIndex }">
                    <slot name="item"
                          :item="item">
                        {{item}}
                    </slot>
                </li>
            </ul>
        </div>
    </div>
</template>
<script>
   // import debounce from 'debounce'

    export default {
        inheritAttributes: true,
        props: {
            options: {
                type: Object,
                default: {}
            },
            onInputChange: {
                type: Function,
                required: true
            },
            onItemSelected: {
                type: Function
            },
            value: {
                type: String,
                required: true
            }
        },
        data () {
            const defaultOptions = {
                debounce: 0,
                placeholder: '',
                inputClass: 'input'
            }
            const extendedOptions = Object.assign({}, defaultOptions, this.options)
            return {
                extendedOptions,
                query: this.value,
                lastSetQuery: null,
                items: [],
                activeItemIndex: -1,
                showItems: false
            }
        },
        beforeMount () {
            // if (this.extendedOptions.debounce !== 0) {
            //     this.onQueryChanged = debounce(this.onQueryChanged, this.extendedOptions.debounce)
            // }
        },
        watch: {
            'query': function (newValue, oldValue) {
                if (newValue === this.lastSetQuery) {
                    this.lastSetQuery = null
                    return
                }
                this.onQueryChanged(newValue)
                this.$emit('input', newValue)
            },
            'value': function (newValue, oldValue) {
                this.setInputQuery(newValue)
            }
        },
        methods: {
            onItemSelectedDefault (item) {
                if (typeof item === 'string') {
                    this.$emit('input', item)
                    this.setInputQuery(item)
                    this.showItems = false
                    // console.log('change value')
                }
            },
            hideItems () {
                setTimeout(() => {
                    this.showItems = false
                }, 300)
            },
            showResults () {
                this.showItems = true
            },
            setInputQuery (value) {
                this.lastSetQuery = value
                this.query = value
            },
            onKeyDown (e) {
                this.$emit('keyDown', e.keyCode)
                switch (e.keyCode) {
                    case 40:
                        this.highlightItem('down')
                        e.preventDefault()
                        break
                    case 38:
                        this.highlightItem('up')
                        e.preventDefault()
                        break
                    case 13:
                        this.selectItem()
                        e.preventDefault()
                        break
                    case 27:
                        this.showItems = false
                        e.preventDefault()
                        break
                    default:
                        return true
                }
            },
            selectItem (index) {
                let item = null
                if (typeof index === 'undefined') {
                    if (this.activeItemIndex === -1) {
                        return
                    }
                    item = this.items[this.activeItemIndex]
                } else {
                    item = this.items[index]
                }
                if (!item) {
                    return
                }
                if (this.onItemSelected) {
                    this.onItemSelected(item)
                } else {
                    this.onItemSelectedDefault(item)
                }
                this.hideItems()
            },
            highlightItem (direction) {
                if (this.items.length === 0) {
                    return
                }
                let selectedIndex = this.items.findIndex((item, index) => {
                    return index === this.activeItemIndex
                })
                if (selectedIndex === -1) {
                    // nothing selected
                    if (direction === 'down') {
                        selectedIndex = 0
                    } else {
                        selectedIndex = this.items.length - 1
                    }
                } else {
                    this.activeIndexItem = 0
                    if (direction === 'down') {
                        selectedIndex++
                        if (selectedIndex === this.items.length) {
                            selectedIndex = 0
                        }
                    } else {
                        selectedIndex--
                        if (selectedIndex < 0) {
                            selectedIndex = this.items.length - 1
                        }
                    }
                }
                this.activeItemIndex = selectedIndex
            },
            setItems (items) {
                this.items = items
                this.activeItemIndex = -1
                this.showItems = true
            },
            onQueryChanged (value) {
                const result = this.onInputChange(value)
                this.items = []
                if (typeof result === 'undefined' || typeof result === 'boolean' || result === null) {
                    return
                }
                if (result instanceof Array) {
                    this.setItems(result)
                } else if (typeof result.then === 'function') {
                    result.then(items => {
                        this.setItems(items)
                    })
                }
            }
        }
    }
</script>

<style>
    .v-suggestions {
        position: relative;
        -webkit-box-sizing: border-box;
        -moz-box-sizing: border-box;
        box-sizing: border-box;
    }

    .v-suggestions .suggestions {
        position: absolute;
        left: 0;
        top: 36px;
        width: 100%;
        z-index: 100;
        background: #ffffff;
    }

    .v-suggestions .items {
        list-style: none;
        border: 1px solid #EEE;
        margin: 0;
        padding: 0;
        border-width: 0 1px 1px 1px;
    }

    .v-suggestions .item {
        border-bottom: 1px solid #eee;
        padding: .4rem;
    }

    .v-suggestions .items .item.is-active, .v-suggestions .items .item:hover {
        background: #eee;
        cursor: pointer;
    }

    .v-suggestions-input {
        -webkit-appearance: none;
        -webkit-box-align: center;
        -ms-flex-align: center;
        align-items: center;
        border: 1px solid transparent;
        border-radius: 3px;
        box-shadow: 0 2px 2px 0 rgba(0, 0, 0, 0.16), 0 0 0 1px rgba(0, 0, 0, 0.08);
        display: -ms-inline-flexbox;
        display: inline-flex;
        font-size: 1rem;
        height: 2.25em;
        -webkit-box-pack: start;
        -ms-flex-pack: start;
        justify-content: flex-start;
        line-height: 1.5;
        padding-bottom: calc(0.375em - 1px);
        padding-left: calc(0.625em - 1px);
        padding-right: calc(0.625em - 1px);
        padding-top: calc(0.375em - 1px);
        position: relative;
        vertical-align: top;
        background-color: white;
        border-color: #dbdbdb;
        color: #363636;
        max-width: 100%;
        width: 100%;
        -webkit-box-sizing: border-box;
        -moz-box-sizing: border-box;
        box-sizing: border-box;
        position: relative;
    }

    .v-suggestions-input:focus, .v-suggestions-input:active {
        box-shadow: 0 3px 3px 0 rgba(0, 0, 0, 0.16), 0 0 0 1px rgba(0, 0, 0, 0.08);
        outline: none;
    }
</style>
