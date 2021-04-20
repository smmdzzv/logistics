<template>
    <b-container>
        <b-card>
            <template #header>
                <div class="d-flex align-baseline">
                    <span>
                        Список поставщиков машин
                    </span>
                    <div class="ml-auto">
                        <div class="d-flex">
                            <input type="text" v-model="newProviderName" placeholder="Название" class="form-control">
                            <a class="btn" href="#" @click.prevent="addProvider">
                                <b-icon-plus-circle></b-icon-plus-circle>
                            </a>
                        </div>
                    </div>

                </div>
            </template>


            <b-table :items="items" :fields="fields" responsive striped>
                <template v-slot:cell(name)="data">
                    <div class="d-flex">
                        <input class="form-control" type="text" v-model="data.item.name">
                        <a class="ml-auto btn" href="#" @click.prevent="update(data.item)">
                            <b-icon-check-circle-fill variant="success"></b-icon-check-circle-fill>
                        </a>
                    </div>
                </template>
                <template v-slot:cell(actions)="data">
                    <div class="d-flex">
                        <a class="btn" href="#" @click.prevent="destroy(data.item)">
                            <b-icon-trash variant="danger"></b-icon-trash>
                        </a>
                    </div>

                </template>
            </b-table>
        </b-card>
    </b-container>

</template>

<script>
import {hideBusySpinner, showBusySpinner} from "../../tools";

export default {
    name: "CarProvidersIndex",
    props: {
        carProviders: Array
    },
    data() {
        return {
            items: this.carProviders,
            fields: [
                {
                    key: 'name',
                    label: 'Имя'
                },
                {
                    key: 'actions',
                    label: ''
                }
            ],
            newProviderName: null
        }
    },
    methods: {
        addProvider() {
            showBusySpinner()
            axios.post('/car-providers', {name: this.newProviderName})
                .then(response => this.items.push(response.data))
                .finally(() => hideBusySpinner())
        },
        update(item) {
            showBusySpinner()
            axios.put('/car-providers/' + item.id, {name: item.name})
                .finally(() => hideBusySpinner())
        },
        destroy(item) {
            this.$bvModal.msgBoxConfirm(
                `Вы уверены что хотите удалить поставщика ${item.name}?`,
                {
                    centered: true,
                    okTitle: 'Да',
                    cancelTitle: 'Отменить',
                    footerClass: 'border-0',
                    title: 'Подтверждение удаления'
                }).then(confirm => {
                if (confirm) {
                    showBusySpinner()
                    axios.delete('/car-providers/' + item.id)
                        .then(_ => {
                            this.items = this.items.filter(it => it.id !== item.id)
                        })
                        .finally(hideBusySpinner())
                }

            })
        }
    }
}
</script>
