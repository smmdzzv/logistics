<template>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-12">
                <div class="card shadow">
                    <div class="card-header">Тарифы</div>
                    <div class="card-body">
                        <section class="pb-5">
                            <form @submit.prevent="postTariff" id="addTariffForm">
                                <h4 class="offset-5 pb-4 col-6">Создать тариф</h4>

                                <b-alert :show="showAddTariffErrorModal" class="text-center" id="addTariffErrorAlert"
                                         variant="danger">
                                    Не удалось добавить тариф. Повторите добавление тарифа после перезагрузки страницы.
                                </b-alert>

                                <div class="form-group row">
                                    <label class="col-md-4 col-form-label text-md-right" for="name">Название</label>
                                    <div class="col-md-6">
                                        <input autocomplete="name"
                                               autofocus
                                               class="form-control"
                                               id="name"
                                               maxlength="255"
                                               name="name"
                                               type="text"
                                               v-model="tariff.name">
                                        <b-popover
                                                :show.sync="$v.tariff.name.$error"
                                                content="Введите название тарифа"
                                                placement="bottom"
                                                target="name"
                                                triggers="null"
                                                variant="danger"/>
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label class="col-md-4 col-form-label text-md-right"
                                           for="description">Описание</label>
                                    <div class="col-md-6">
                                        <input autocomplete="description"
                                               autofocus
                                               class="form-control"
                                               id="description"
                                               maxlength="255"
                                               name="description"
                                               type="text"
                                               v-model="tariff.description">
                                        <b-popover
                                                :show.sync="$v.tariff.description.$error"
                                                content="Максимально допустимая длина описания 255 символов"
                                                placement="bottom"
                                                target="description"
                                                triggers="null"
                                                variant="danger"/>
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label for="branch_id" class="col-md-4 col-form-label text-md-right">Филиал</label>
                                    <div class="col-md-6">
                                        <select id="branch_id" name="branch_id"
                                                class="form-control"
                                                v-model="tariff.branch"
                                                :class="{'is-invalid': !tariff.branch}">
                                            <option value="null" disabled>--Выберите филиал--</option>
                                            <option v-for="branch in branches" :value="branch">{{branch.name}}</option>
                                        </select>

                                        <b-popover
                                                :show.sync="$v.tariff.branch.$error"
                                                content="Выберите филиал"
                                                placement="bottom"
                                                target="branch_id"
                                                triggers="null"
                                                variant="danger"/>
                                    </div>
                                </div>

                                <div class="form-group row mb-0">
                                    <div class="col-md-6 offset-md-4">
                                        <button class="btn btn-primary" type="submit" :disabled="isSubmitting">
                                            Сохранить
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </section>
                        <section>
                            <div class="row">
                                <div class="col-12 text-right">
                                    <a href="tariff-price-histories/create">Обновить расценки</a>

                                </div>
                            </div>
                            <div class="card">
                                <div class="card-header bg-secondary">
                                    <div class="text-light p-1">Список тарифов</div>
                                </div>
                                <div>
                                    <ul class="list-group list-group-flush">
                                        <li class="list-group-item"
                                            v-bind:property="tariffs"
                                            v-for="tariff in tariffs">
                                            <div :key="tariff.id" class="row">
                                                <div class="col-md-3"> {{tariff.name}}</div>
                                                <div class="col-md-3"> {{tariff.branch.name}}</div>
                                                <div class="col-md-4"> {{tariff.description}}</div>
                                                <div class="col-md-2">
                                                    <div class="row mt-2 mt-md-0">
                                                        <img :id="tariff.id" @click="editTariffPricing(tariff)"
                                                             alt="delete-item"
                                                             class="icon-btn-sm ml-auto"
                                                             src="/svg/edit.svg">
                                                        <b-tooltip :target="tariff.id" triggers="hover">
                                                            Редактировать цены
                                                        </b-tooltip>
                                                        <img @click="removeFromList(tariff)" alt="delete-item"
                                                             class="icon-btn-sm mx-auto"
                                                             src="/svg/delete.svg">
                                                    </div>
                                                </div>
                                            </div>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </section>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    import {required, maxLength} from 'vuelidate/lib/validators/';

    export default {
        name: "TariffEditor",
        props: {
            data: {
                type: Array,
                required: true
            },
            branches: {
                type: Array,
                required: true
            }
        },
        data() {
            return {
                tariff: {
                    name: '',
                    description: '',
                    branch: null
                },
                tariffs: this.data,
                showAddTariffErrorModal: false,
                isSubmitting: false

            }
        },
        methods: {
            async postTariff() {
                this.setBusy();
                if (this.$v.$invalid)
                    this.$v.$touch();
                else {
                    try {
                        const response = await axios.post('/tariffs', {
                            name: this.tariff.name,
                            description: this.tariff.description,
                            branch_id: this.tariff.branch.id
                        });

                        if (response.data) {
                            this.tariffs.push(response.data);
                            this.resetForm();
                        } else
                            this.showAddTariffErrorModal = true;
                    } catch (e) {
                        this.showAddTariffErrorModal = true;
                    }
                }
                this.$nextTick(() => {
                    this.hideBusy();
                })
            },
            setBusy() {
                this.isSubmitting = true;
                this.$bvModal.show('busyModal');
            },
            hideBusy() {
                this.isSubmitting = false;
                this.$bvModal.hide('busyModal');
            },
            resetForm() {
                this.showAddTariffErrorModal = false;
                this.tariff.name = '';
                this.tariff.description = '';
                this.tariff.branch = null;
                this.$nextTick(() => {
                    this.$v.$reset();
                })
            },
            async removeFromList(tariff) {
                let confirmed = await this.$bvModal.msgBoxConfirm(`Вы уверены что хотите удалить тариф ${tariff.name}?`, {
                    centered: true,
                    okTitle: 'Да',
                    cancelTitle: 'Отменить',
                    footerClass: 'border-0',
                    title: 'Подтверждение удаления'
                });

                if (!confirmed)
                    return;

                this.setBusy();
                try {
                    await axios.delete('/tariffs/' + tariff.id);

                    this.tariffs = jQuery.grep(this.tariffs, function (value) {
                        return value !== tariff;
                    });
                } catch (e) {
                    //TODO
                }
                this.hideBusy();
            },
            editTariffPricing(tariff) {
                window.location = `tariff-price-histories/create?tariff=${tariff.id}`;
            }
        },
        validations: {
            tariff: {
                name: {
                    required,
                    maxLength: maxLength(255)
                },
                description: {
                    maxLength: maxLength(255)
                },
                branch:{
                    required
                }
            }
        }
    }
</script>

<style scoped>

</style>
