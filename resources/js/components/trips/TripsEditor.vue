<template>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-10 col-lg-8">
                <div class="card shadow mb-5">
                    <div class="card-header">
                        <span v-if="isEditMode">Редактировать</span>
                        <span v-else>Создать</span> рейс
                    </div>
                    <div class="card-body">
                        <form @submit.prevent id="addTripForm">
                            <b-alert :show="showAddTripErrorModal" class="text-center" id="addTariffErrorAlert"
                                     variant="danger">
                                Не удалось создать рейс. Повторите создать тариф после перезагрузки страницы.
                            </b-alert>

                            <div class="form-group row">
                                <label class="col-md-4 col-form-label text-md-right" for="code">Номер рейса</label>
                                <div class="col-md-6">
                                    <input :class="{'is-invalid':$v.data.code.$error || errors.code}"
                                           autocomplete="code"
                                           autofocus
                                           class="form-control"
                                           id="code"
                                           maxlength="255"
                                           name="code"
                                           placeholder="Введите код рейса"
                                           type="text"
                                           v-model="data.code">

                                    <span class="invalid-feedback" role="alert"
                                          v-if="$v.data.code.$error || errors.code">
                                        <strong>Необходимо ввести номер рейса</strong>
                                        <strong v-for="message in errors.code">{{message}}</strong>
                                    </span>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-md-4 col-form-label text-md-right" for="carId">Машина</label>
                                <div class="col-md-6">
                                    <suggestion-input :initQuery="data.car.number"
                                                      :isInvalid="$v.data.car.$error || errors.car"
                                                      :onItemSearchInputChange="onCarSearchChange"
                                                      :onSelected="onCarSelected"
                                                      :options="filteredCars"
                                                      displayPropertyName="number"
                                                      id="carId"
                                                      placeholder="Введите номер машины"/>
                                    <input class="is-invalid form-control" type="hidden">
                                    <span class="invalid-feedback" role="alert"
                                          v-if="$v.data.car.$error || errors.car">
                                        <strong>Необходимо выбрать машину.</strong>
                                        <strong v-for="message in errors.car">{{message}}.</strong>
                                    </span>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-md-4 col-form-label text-md-right" for="driverId">Водитель</label>
                                <div class="col-md-6">
                                    <search-user-dropdown :isInvalid="$v.data.driver.$error || errors.driver"
                                                          :preselectedUser="data.driver"
                                                          :selected="driverSelected"
                                                          url="/concrete/driver/filter?userInfo="
                                                          id="driverId"
                                                          placeholder="Введите ФИО или код водителя"/>
                                    <input class="is-invalid form-control" type="hidden">
                                    <span class="invalid-feedback" role="alert"
                                          v-if="$v.data.driver.$error || errors.driver">
                                        <strong>Необходимо выбрать водителя.</strong>
                                        <strong v-for="message in errors.driver">{{message}}.</strong>
                                    </span>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-md-4 col-form-label text-md-right" for="departureDate">Дата
                                    отправления</label>
                                <div class="col-md-6">
                                    <b-form-input
                                        :class="{'is-invalid': $v.data.departureDate.$error || errors.departureDate}"
                                        id="departureDate"
                                        type="date"
                                        v-model="data.departureDate"/>

                                    <span class="invalid-feedback"
                                          role="alert"
                                          v-if="$v.data.departureDate.$error || errors.departureDate">
                                        <strong>Необходимо указать дату отправления.</strong>
                                        <strong v-for="message in errors.departureDate">{{message}}.</strong>
                                    </span>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-md-4 col-form-label text-md-right" for="returnDate">Дата
                                    возвращения</label>
                                <div class="col-md-6">
                                    <b-form-input
                                        :class="{'is-invalid': $v.data.returnDate.$error || errors.returnDate}"
                                        id="returnDate"
                                        type="date"
                                        v-model="data.returnDate"></b-form-input>

                                    <span class="invalid-feedback" role="alert"
                                          v-if="$v.data.returnDate.$error || errors.returnDate">
                                        <strong>Необходимо указать дату возвращения.</strong>
                                        <strong v-for="message in errors.returnDate">{{message}}.</strong>
                                    </span>
                                </div>
                            </div>

                            <div class="form-group row mb-0">
                                <div class="col-md-6 offset-md-4">
                                    <button @click="submitForm" class="btn btn-primary">
                                        <span v-if="data.id">Сохранить</span>
                                        <span v-else>Создать</span>
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                <div class="shadow" v-if="isEditMode">
                    <stored-table :branches="branches"
                                  flowablePagination
                                  selectable/>
                </div>
            </div>
        </div>

    </div>


</template>

<script>
    import {required, maxLength} from 'vuelidate/lib/validators/';

    export default {
        name: "TripsEditor",
        mounted() {
            this.filteredCars = this.cars;
        },
        props: {
            cars: {
                type: Array,
                required: true
            },
            trip: {
                type: Object,
                required: false,
                default: () => ({
                    code: null,
                    car: {
                        number:null
                    },
                    driver: null,
                    departureDate: null,
                    returnDate: null
                })
            },
            branches: {
                type: Array,
                required: false
            },
            isEditMode: {
                type: Boolean,
                default: false
            }
        },
        data() {
            return {
                data: this.trip,
                showAddTripErrorModal: false,
                filteredCars: [],
                errors: {
                    code: null,
                    driver: null,
                    car: null,
                    departureDate: null,
                    returnDate: null
                }
            }
        },
        methods: {
            onItemsSelected(items) {
                console.log(items)
            },
            onCarSearchChange(query) {
                this.filteredCars = this.cars.filter(function (car) {
                    return car.number.toLowerCase().includes(query.toLowerCase())
                });
            },
            onCarSelected(car) {
                this.data.car = car;
            },
            driverSelected(driver) {
                this.data.driver = driver;
            },
            submitForm() {
                if (this.$v.$invalid)
                    this.$v.$touch();
                else
                    this.saveTrip()

            },
            async saveTrip() {
                try {
                    let data = {
                        code: this.data.code,
                        carId: this.data.car.id,
                        driverId: this.data.driver.id,
                        departureDate: this.data.departureDate,
                        returnDate: this.data.returnDate,
                        id: this.data.id
                    };

                    const response = await axios.post('/trips', data);
                    window.location = getBaseUrl() + '/trips/' + response.data.id;

                } catch (e) {
                    if (e.response.status === 422) {
                        this.errors.code = e.response.data.errors.code;
                        this.errors.car = e.response.data.errors.carId;
                        this.errors.driver = e.response.data.errors.driverId;
                        this.errors.departureDate = e.response.data.errors.departureDate;
                        this.errors.returnDate = e.response.data.errors.returnDate;
                    } else {
                        this.$root.showErrorMsg(
                            "Ошибка сохранения",
                            "Не удалось сохранить изменения. Перезагрузите страницу и попробуйте еще раз"
                        )
                    }
                }
            }
            // async getCars(){
            //     try{
            //         const response = await axios.get('/cars/all')
            //
            //     }
            //     catch (e) {
            //         this.$root.showErrorMsg('Ошибка загрузки',
            //         'Не удалось загрузить список машин. Перезагрузите страницу и попробуйте еще раз')
            //     }
            // }
        },
        components: {
            'SuggestionInput': require('../common/SuggestionInput.vue').default,
            'SearchUserDropdown': require('../users/SearchUserDropdown.vue').default
        },
        validations: {
            data: {
                code: {
                    required,
                    maxLength: maxLength(10)
                },
                car: {
                    type: Object,
                    required
                },
                driver: {
                    type: Object,
                    required
                },
                departureDate: {
                    required
                },
                returnDate: {
                    required
                }
            }
        }
    }

</script>
