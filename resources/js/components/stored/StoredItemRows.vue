<template>
    <div>
        <div class="container-fluid" v-for="storedItem in storedItems">
            <div class="row py-2 align-items-baseline"
                 :class="{'table-danger': storedItem.status === 'lost',
                          'table-secondary': storedItem.status === 'deleted',
                          'table-primary': storedItem.status === 'stored',
                          'table-warning': storedItem.status === 'transit',
                          'table-success': storedItem.status === 'delivered'
                         }">
                <div class="font-weight-bolder ml-auto pr-5">{{ storedItem.code }}</div>
                <a class="btn mr-2" :href="'/stored-items/' + storedItem.id">
                    <b-icon-file-earmark-text></b-icon-file-earmark-text>
                </a>
                <a v-if="storedItem.status !== 'lost'
                            && storedItem.status !== 'delivered'
                            && storedItem.status !== 'deleted'"
                   class="btn" :id="storedItem.id" href="#"
                   @click.prevent="onLostItemSelected(storedItem)">
                    <b-icon-question-diamond></b-icon-question-diamond>
                </a>
            </div>
        </div>

        <b-modal
            @hidden="resetModal"
            @ok.prevent="submitLostStoredItems"
            centered
            ok-title="Сохранить"
            cancel-title="Отменить"
            id="compensationModal"
            ref="compensationModal"
            title="Оформление утерянных товаров"
        >
            <b-form-group
                :state="compensationModalState"
                invalid-feedback="Необходимо ввести  компенсацию за товары"
                label="Полная компенсация в долларах"
                label-for="compensation"
            >
                <b-form-input
                    id="compensation"
                    required
                    step="0.01"
                    min="0"
                    type="number"
                    v-model="compensation"
                ></b-form-input>
            </b-form-group>
        </b-modal>
    </div>
</template>

<script>
export default {
    name: "StoredItemRows",
    props: {
        storedItems: Array
    },
    data() {
        return {
            lostItem: null,
            compensation: 0
        }
    },
    methods: {
        onLostItemSelected(item) {
            this.lostItem = item;
            this.$bvModal.show('compensationModal');
        },
        resetModal() {
            this.compensation = 0;
            this.lostItem = null;
            this.lostStoredItemsCount = 0;
        },
        submitLostStoredItems() {
            axios.post('/lost-stored-items/' + this.lostItem.id, {
                compensation: this.compensation
            })
                .then(response => {
                    window.location.href = `/payment/${response.data}`;
                })
                .catch(e => {
                    if (e.response && e.response.status === 422) {
                        this.$root.showErrorMsg('Ошибка сохранения',
                            e.response.data.message)
                    } else {
                        this.$root.showErrorMsg('Ошибка сохранения',
                            'Повторите попытку после перезагрузки страницы')
                    }
                })
                .then(_ => {
                        this.$bvModal.hide('compensationModal');
                    }
                );
        }
    },
    computed: {
        compensationModalState() {
            return Boolean(this.compensation && this.lostStoredItemsCount > 0);
        }
    }
}
</script>
