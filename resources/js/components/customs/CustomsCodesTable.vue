<template>
    <table-card
        :borderless="borderless"
        :fields="fields"
        :fixed="fixed"
        :hover="hover"
        :items="items"
        :responsive="responsive"
        :select-mode="selectMode"
        :selectable="selectable"
        :sticky-header="tableHeight"
        :striped="striped"
        :tableBusy="false"
        excelFileName="Список таможенных кодов"
        excelSheetName="Лист 1"
        primaryKey="id"
        responsive>

        <template v-slot:cell(isCalculatedByPiece)="{item}">
            <span v-if="item.tax.isCalculatedByPiece">
                ✓
            </span>
            <span v-else>
            </span>
        </template>

        <template v-slot:cell(edit)="{item}">
            <div class="d-flex">
                <a class="mr-2" :href="'/customs-code/' + item.id + '/edit'">
                    <img class="icon-btn-sm" src="/svg/edit.svg">
                </a>
                <a href="#" @click.prevent="deleteCode(item)">
                    <img class="icon-btn-sm" src="/svg/delete.svg">
                </a>
            </div>
        </template>

        <template #header>
            <div class="row align-items-baseline">
                <div class="col-12 col-md-6 mb-3 mb-md-0">
                    <div class=" mr-auto">Коды</div>
                </div>
            </div>
        </template>

    </table-card>
</template>

<script>
    import TableCardProps from '../common/TableCardProps.vue'
    import {hideBusySpinner, showBusySpinner} from "../../tools";

    export default {
        name: "CustomsCodesTable",
        mixins: [TableCardProps],
        props: {
            customCodes: {
                type: Array,
                requested: true
            }
        },
        mounted() {
            this.items = this.customCodes;
        },
        data() {
            return {
                items: [],
                fields: [
                    {
                        key:'name',
                        label: 'Наименование',
                        sortable: true
                    },
                    {
                        key: 'code',
                        label: 'Код',
                        sortable: true
                    },
                    {
                        key: 'tax.price',
                        label: 'Цена',
                        sortable: true
                    },
                    {
                        key: 'tax.interestRate',
                        label: 'Базовая ставка, %',
                        sortable: true
                    },
                    {
                        key: 'tax.vat',
                        label: 'НДС, %',
                        sortable: true
                    },
                    {
                        key: 'tax.totalRate',
                        label: 'Итоговая ставка, %',
                        sortable: true
                    },
                    {
                        key: 'isCalculatedByPiece',
                        label: 'Расчет по шт.',
                        sortable: true
                    },
                    {
                        key: 'edit',
                        label: ''
                    }
                ]
            }
        },
        methods: {
            deleteCode(item){
                this.$bvModal.msgBoxConfirm('Вы действительно хотите удалить таможенный код '
                    + item.name + ' - ' + item.code + '?',{
                    centered:true,
                    cancelTitle: 'Отмена',
                    okTitle: 'Удалить',
                    okVariant: 'danger'
                })
                    .then(confirm => {
                        if (confirm) {
                            showBusySpinner();
                            axios.post('/customs-code/' + item.id,{
                                _method:'delete'
                            })
                                .then(_ => {
                                    this.$bvToast.toast('Таможенный код '
                                        + item.name + ' - ' + item.code
                                        + ' удален', {
                                        title: 'Успешно удалено',
                                        autoHideDelay: 5000,
                                        appendToast: true,
                                        variant: 'success'
                                    });

                                    this.items = this.items.filter(i => i.id !== item.id)
                                })
                                .catch(e => {
                                    let message = 'Не удалось удалить таможенный код';

                                    if (e.response.status === 422)
                                        message = e.response.data.message

                                    this.$bvToast.toast(message, {
                                        title: 'Ошибка удаления',
                                        autoHideDelay: 5000,
                                        appendToast: true,
                                        variant: 'danger'
                                    });
                                })
                                .then(_ => this.$nextTick(_ => hideBusySpinner()))
                        }
                    })
                    .catch(err => {
                        console.log(err)
                    })
            },
        },
        components: {
            'TableCard': require('../common/TableCard.vue').default
        }

    }
</script>
