<template>
    <div>
        <stored-item-info-table v-if="storedItems"
                                @onItemsSelected="onItemsSelected"
                                :providedStoredItems="storedItems"
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
            }
        },
        data() {
            return {
                storedItems: null,
                selectedItems: [],
                actionUrl: null
            }
        },
        methods: {
            initialize() {
                switch (this.action) {
                    case 'load':
                        this.storedItems = this.trip.unloadedItems;
                        this.actionUrl = `/trip/${this.trip.id}/stored-items/load`;
                        break;
                }
            },
            onItemsSelected(items) {
                this.selectedItems = items;
            },
            async submit() {
                try {
                    let data = {
                        storedItems: this.selectedItems.map((selected) => {
                            return selected.id;
                        })
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