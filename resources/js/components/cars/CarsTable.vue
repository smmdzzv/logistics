<template>
    <div>
        <b-table :busy="isBusy"
                 :fields="fields"
                 :items="items"
                 borderless
                 primary-key="id"
                 responsive
                 striped>
            <template v-slot:table-busy>
                <div class="text-center text-info my-2">
                    <b-spinner class="align-middle"></b-spinner>
                </div>
            </template>

            <template slot="buttons" slot-scope="data">
                <div class="row">
                    <a :href="getShowUrl(data.item)" class="btn">
                        <img class="icon-btn-sm" src="/svg/file.svg" alt="car details">
                    </a>

                    <a :href="getEditUrl(data.item)" class="btn">
                        <img class="icon-btn-sm" src="/svg/edit.svg" alt="edit car">
                    </a>

                    <a @click.prevent="deleteCar(data.item)" class="btn" href="#">
                        <img class="icon-btn-sm" src="/svg/delete.svg" alt="delete car">
                    </a>
                </div>

            </template>
        </b-table>
    </div>
</template>

<script>
    export default {
        name: "CarsTable",
        props: {
            cars: {
                type: Array,
                required: true
            }
        },
        data() {
            return {
                items: this.cars,
                isBusy: false,
                fields: {
                    number: {
                        label: 'Гос. номер'
                    },
                    serial: {
                        label: 'УКТ (ВИН)'
                    },
                    trailerNumber: {
                        label: 'Номер прицепа'
                    },
                    length: {
                        label: 'Длина'
                    },
                    width: {
                        label: 'Ширина'
                    },
                    height: {
                        label: 'Высота'
                    },
                    maxWeight: {
                        label: 'Груз-ть, кг'
                    },
                    maxCubage: {
                        label: 'Объем, куб'
                    },
                    'buttons': {
                        label: ''
                    }
                }
            }
        },
        methods: {
            getEditUrl(car) {
                return `/cars/${car.id}/edit`;
            },
            getShowUrl(car){
                return `/cars/${car.id}`;
            },
            async deleteCar(car) {
                this.isBusy = true;
                let confirm = await this.$bvModal.msgBoxConfirm(`Вы уверены что хотите удалить машину ${car.number}?`, {
                    centered: true,
                    okTitle: 'Да',
                    cancelTitle: 'Отменить',
                    footerClass: 'border-0',
                    title: 'Подтверждение удаления'
                });

                if (confirm) {
                    try {
                        const response = axios.delete('/cars/' + car.id);
                        this.items = $.grep(this.items, function (item) {
                            return item.id !== car.id;
                        });
                    } catch (e) {
                        await this.$bvModal.msgBoxOk(`Не удалось удалить машину  ${car.number}. Перезагрузите страницу и попробуйте еще раз`, {
                            centered: true,
                            okTitle: 'Закрыть',
                            footerClass: 'border-0',
                            title: 'Сообщение об ошибке'
                        });
                    }
                }
                this.$nextTick(() => {
                    this.isBusy = false;
                })
            }
        }
    }
</script>

<style scoped>

</style>
