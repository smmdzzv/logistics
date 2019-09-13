<template>
    <div>
        <form @submit.prevent>
            <div class="form-group row">
                <label class="col-md-4 col-form-label text-md-right" for="name">Название</label>
                <div class="col-md-6">
                    <input :class="{'is-invalid':$v.data.name.$error || errors.name}"
                           autocomplete="name"
                           autofocus
                           class="form-control"
                           id="name"
                           name="name"
                           placeholder="например, ИВУ"
                           type="text"
                           v-model="data.name">

                    <span class="invalid-feedback" role="alert" v-if="$v.data.name.$error">
                        <strong>Необходимо ввести уникальное имя филиала</strong>
                    </span>

                    <span class="invalid-feedback" role="alert" v-if="errors.name">
                        <strong v-for="message in errors.name">{{message}}</strong>
                    </span>
                </div>
            </div>
            <div class="form-group row">
                <label class="col-md-4 col-form-label text-md-right">Страна</label>
                <div class="col-md-6">
                    <b-form-select :class="{'is-invalid':$v.data.country.$error  || errors.country}"
                                   v-model="data.country">
                        <template v-slot:first>
                            <option :value="null" disabled>-- Выберите страну --</option>
                        </template>
                        <option :key="country.id" :value="country" v-for="country in countries">{{country.name}}
                        </option>
                    </b-form-select>

                    <span class="invalid-feedback" role="alert" v-if="$v.data.country.$error">
                        <strong>Необходимо выбрать страну</strong>
                    </span>

                    <span class="invalid-feedback" role="alert" v-if="errors.country">
                         <strong v-for="message in errors.country">{{message}}</strong>
                    </span>
                </div>
            </div>
            <div class="form-group row">
                <label class="col-md-4 col-form-label text-md-right">Директор</label>
                <div class="col-md-6">
                    <search-user-dropdown :isInvalid="errors.director && errors.director.length>0"
                                          :preselectedUser="data.director"
                                          :selected="onUserSelected"
                                          placeholder="Введите ФИО или код сотрудника"
                                          ref="searchUserDropdown"/>

                    <input class="is-invalid form-control" type="hidden">
                    <span class="invalid-feedback" role="alert" v-if="errors.director">
                         <strong v-for="message in errors.director">{{message}}</strong>
                    </span>
                </div>
            </div>
        </form>
        <div class="form-group row mb-0">
            <div class="col-md-6 offset-md-4">
                <button @click="submitForm" class="btn btn-primary">
                    <span v-if="data.id">Сохранить</span>
                    <span v-else>Добавить</span>
                </button>
            </div>
        </div>
    </div>
</template>

<script>
    import {required, maxLength} from 'vuelidate/lib/validators';

    export default {
        name: "BranchEditor",
        mounted() {
            this.getCountries();
        },
        props: {
            branch: {
                type: Object,
                required: false,
                default: () => ({
                    name: null,
                    country: null,
                    id: null,
                    director: null
                }),
            }
        },
        data() {
            return {
                data: this.branch,
                countries: [],
                errors: {
                    name: null,
                    country: null,
                    director: null
                }
            }
        },
        methods: {
            onUserSelected(user) {
                this.data.director = user;
            },
            async getCountries() {
                try {
                    const response = await axios.get('/countries');
                    this.countries = response.data;
                } catch (e) {
                    this.$root.showErrorMsg(
                        "Ошибка загрузки стран",
                        "Для редактирования филиалов, необходим список стран. Перезагрузите страницу и попробуйте еще раз"
                    )
                }
            },
            async storeBranch(data) {
                // return await axios.post('/branch', {
                //     name: this.data.name,
                //     director: this.data.director.id,
                //     country: this.data.country.id
                // })

                return await axios.post('/branch', data)
            },
            async updateBranch(data) {
                // return await axios.patch(`/branch/${this.branch.id}`, {
                //     name: this.data.name,
                //     director: this.data.director.id,
                //     country: this.data.country.id
                // })
                return await axios.patch(`/branch/${this.branch.id}`, data)
            },
            async submitForm() {
                if (this.$v.$invalid)
                    this.$v.$touch();
                else {
                    this.$bvModal.show('busyModal');
                    let response;
                    try {
                        let data = {
                            name: this.data.name,
                            country: this.data.country.id
                        };
                        if(this.data.director)
                            data.director = this.data.director.id;

                        if (this.data.id) {
                            response = await this.updateBranch(data);
                            this.$emit('branchUpdated', response.data)
                        } else {
                            response = await this.storeBranch(data);
                            this.$emit('branchSaved', response.data)
                        }
                        this.clearForm();
                    } catch (e) {
                        if (e.response.status === 422) {
                            this.errors.name = e.response.data.errors.name;
                            this.errors.country = e.response.data.errors.country;
                            this.errors.director = e.response.data.errors.director;
                        } else {
                            this.$root.showErrorMsg(
                                "Ошибка сохранения",
                                "Не удалось сохранить изменения. Перезагрузите страницу и попробуйте еще раз"
                            )
                        }

                    }
                    this.$nextTick(() => {
                        this.$bvModal.hide('busyModal');
                    })
                }
            },
            clearForm() {
                this.errors.name = null;
                this.errors.country = null;
                this.errors.director = null;

                this.data.id = null;
                this.data.name = null;
                this.data.country = null;
                this.data.director = null;

                this.$refs.searchUserDropdown.resetInput();
                this.$nextTick(() => {
                    this.$v.$reset();
                })
            }
        },
        computed: {
            action() {
                let action;
                if (this.data.id)
                    action = '/branch/'
            }
        },
        components: {
            'SearchUserDropdown': require('../users/SearchUserDropdown.vue').default
        },
        validations: {
            data: {
                name: {
                    required,
                    maxLength: maxLength(255)
                },
                country: {
                    required
                }
            }
        }
    }
</script>
