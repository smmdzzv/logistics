<template>
    <div>
        <stored-item-info-table v-if="storedItems"
                                @onItemsSelected="onItemsSelected"
                                @branchSelected="onBranchSelected"
                                :providedStoredItems="storedItems"
                                :branches="branches"
                                prevent-item-loading
                                flowable>
        </stored-item-info-table>
        <div class="row my-4">
            <div class="col-12 text-center">
                <button @click="submit" class="btn btn-primary">Сохранить</button>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        name: "TripItemsEditor",
        mounted() {
            this.initialize();
        },
        props: {
            trip: {
                type: Object,
                required: true
            },
            action: {
                type: String,
                required: true
            },
            branches:{
                type: Array
            }
        },
        data() {
            return {
                storedItems: null,
                selectedItems: [],
                actionUrl: null,
                selectedBranch:null
            }
        },
        methods: {
            initialize() {
                switch (this.action) {
                    case 'load':
                        this.storedItems = this.trip.unloadedItems;
                        this.actionUrl = `/trip/${this.trip.id}/stored-items/load`;
                        break;
                    case 'unload':
                        this.storedItems = this.trip.loadedItems;
                        this.actionUrl = `/trip/${this.trip.id}/stored-items/unload`;
                        break;
                }
            },
            onItemsSelected(items) {
                this.selectedItems = items;
            },
            onBranchSelected(branch){
                this.selectedBranch = branch;
            },
            validate(){
                let result = true;
                switch(this.action){
                    case 'unload':
                        result = this.validateBranch();
                        break;
                }
                return result;
            },
            validateBranch(){
                if(this.selectedBranch)
                    return true;
                else{
                    this.$root.showErrorMsg(
                        'Выберите филиал',
                        'Для приема товаров необходимо выбрать филиал'
                    );
                    return false;
                }
            },
            async submit() {
                if(!this.validate())
                    return;
                try {
                    let data = {
                        storedItems: this.selectedItems.map((selected) => {
                            return selected.id;
                        }),
                        branch: this.selectedBranch ? this.selectedBranch.id : null
                    };

                    const response = await axios.post(this.actionUrl, data);
                    window.location = `/trips/${this.trip.id}`;
                } catch (e) {
                    this.$root.showErrorMsg(
                        'Ошибка сохранения',
                        'Не удалось загрузить товары на рейс. Повторите после обновления страницы'
                    );
                }
            }
        }
    }
</script>

<style scoped>

</style>