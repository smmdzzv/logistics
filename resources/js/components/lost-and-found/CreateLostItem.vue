//TODO finish this
<template>
    <div>
        <div class="col-12">
            <suggestion-input :options="storedItems"
                              placeholder="Введите код товара"
                              displayPropertyName="name"
                              keyPropertyName="id"
                              :onItemSearchInputChange="updateStoredItems"
                              :onSelected="storedItemSelected">

            </suggestion-input>
        </div>
    </div>
</template>

<script>
    import SuggestionInput from "../common/SuggestionInput";

    export default {
        name: "CreateLostItem",
        components: {SuggestionInput},
        data() {
            return {
                storedItems: [],

            }
        },
        methods: {
            async updateStoredItems() {

            },
            storedItemSelected(){

            }
        },
        comments: {
            'SuggestionInput': require('../common/SuggestionInput.vue').default
        }
    }
</script>

