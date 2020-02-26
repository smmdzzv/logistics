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
                                <label for="departure_branch"
                                       class="col-md-4 col-form-label text-md-right">
                                    Филиал отправления</label>

                                <div class="col-md-6">
                                    <select id="departure_branch"
                                            class="form-control custom-select"
                                            :class="{'is-invalid':$v.data.departure_branch.$error || errors.departure_branch}"
                                            name="departure_branch"
                                            v-model="data.departure_branch"
                                            required>
                                        <option value="null" disabled>-- Выберите филиал отправления --</option>
                                        <option v-for="branch in branches" :value="branch">{{branch.name}}</option>
                                    </select>
                                    <span class="invalid-feedback" role="alert"
                                          v-if="$v.data.departure_branch.$error || errors.departure_branch">
                                        <strong>Необходимо выбрать филиал отправления.</strong>
                                        <strong v-for="message in errors.departure_branch">{{message}}.</strong>
                                    </span>
                                </div>
                            </div>


                            <div class="form-group row">
                                <label for="destination_branch"
                                       class="col-md-4 col-form-label text-md-right">
                                    Филиал назначения</label>

                                <div class="col-md-6">
                                    <select id="destination_branch"
                                            class="form-control custom-select"
                                            :class="{'is-invalid':$v.data.destination_branch.$error || errors.destination_branch}"
                                            name="destination_branch"
                                            v-model="data.destination_branch"
                                            required>
                                        <option value="null" disabled>-- Выберите филиал назначения --</option>
                                        <option v-for="branch in branches" :value="branch">{{branch.name}}</option>
                                    </select>
                                    <span class="invalid-feedback" role="alert"
                                          v-if="$v.data.destination_branch.$error || errors.destination_branch">
                                        <strong v-if="$v.data.destination_branch.required">
                                            Необходимо выбрать филиал назначения.
                                            Пункт назначения не должен совпадать с пунктом отправления</strong>
                                        <strong v-for="message in errors.destination_branch">{{message}}.</strong>
                                    </span>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-md-4 col-form-label text-md-right" for="mileageBefore">Пробег машины до отправки</label>
                                <div class="col-md-6">
                                    <input
                                        :class="{'is-invalid':$v.data.mileageBefore.$error || errors.mileageBefore}"
                                        autocomplete="mileageBefore"
                                        autofocus
                                        class="form-control"
                                        id="mileageBefore"
                                        name="mileageBefore"
                                        type="number"
                                        min="0"
                                        v-model="data.mileageBefore">

                                    <span class="invalid-feedback" role="alert"
                                          v-if="$v.data.mileageBefore.$error || errors.mileageBefore">
                                        <strong>Необходимо ввести длину маршрута в км</strong>
                                        <strong v-for="message in errors.mileageBefore">{{message}}</strong>
                                    </span>
                                </div>
                            </div>

                            <!--                            <div class="form-group row justify-content-center" v-if="data.car.trailerNumber">-->
                            <!--                                <div class="input-group offset-md-2 col-md-6">-->
                            <!--                                    <div class="input-group-prepend">-->
                            <!--                                        <div class="input-group-text">-->
                            <!--                                            <input type="checkbox" v-model="data.hasTrailer" name="hasTrailer">-->
                            <!--                                        </div>-->
                            <!--                                    </div>-->
                            <!--                                    <input type="text" class="form-control"-->
                            <!--                                           value="С прицепом" disabled>-->
                            <!--                                </div>-->
                            <!--                            </div>-->

                            <div class="form-group row" v-if="data.car.trailerNumber">
                                <label class="col-md-4 col-form-label text-md-right">Наличие прицепа</label>
                                <div class="col-md-6">
                                    <b-input-group v-b-tooltip.hover
                                                   title="Учитывается при расчете грузоподъемности и кубатуры машины">
                                        <b-input-group-prepend is-text>
                                            <b-form-checkbox switch class="mr-n2" v-model="data.hasTrailer"
                                                             name="hasTrailer">
                                                <span class="sr-only">Switch for following text input</span>
                                            </b-form-checkbox>
                                        </b-input-group-prepend>
                                        <b-form-input disabled value="С прицепом"></b-form-input>
                                    </b-input-group>
                                </div>
                            </div>

                            <!--                            <div class="form-group row">-->
                            <!--                                <label class="col-md-4 col-form-label text-md-right">Машина без груза</label>-->
                            <!--                                <div class="col-md-6">-->
                            <!--                                    <b-input-group  v-b-tooltip.hover title="Учитывается при расчете расхода топлива">-->
                            <!--                                        <b-input-group-prepend is-text>-->
                            <!--                                            <b-form-checkbox switch v-model="data.emptyToDestination" class="mr-n2">-->
                            <!--                                                <span class="sr-only">Switch for following text input</span>-->
                            <!--                                            </b-form-checkbox>-->
                            <!--                                        </b-input-group-prepend>-->
                            <!--                                        <b-form-input disabled value="До филиала назначения"></b-form-input>-->
                            <!--                                    </b-input-group>-->
                            <!--                                </div>-->
                            <!--                            </div>-->

                            <!--                            <div class="form-group row">-->
                            <!--                                <label class="col-md-4 col-form-label text-md-right">Машина без груза</label>-->
                            <!--                                <div class="col-md-6">-->
                            <!--                                    <b-input-group v-b-tooltip.hover title="Учитывается при расчете расхода топлива">-->
                            <!--                                        <b-input-group-prepend is-text>-->
                            <!--                                            <b-form-checkbox v-model="data.emptyFromDestination" switch class="mr-n2">-->
                            <!--                                                <span class="sr-only">Switch for following text input</span>-->
                            <!--                                            </b-form-checkbox>-->
                            <!--                                        </b-input-group-prepend>-->
                            <!--                                        <b-form-input disabled value="От филиала назначения"></b-form-input>-->
                            <!--                                    </b-input-group>-->
                            <!--                                </div>-->
                            <!--                            </div>-->
                            <hr>
                            <div class="form-group row">
                                <label class="col-md-4 col-form-label text-md-right" for="routeLengthToDestination">Длина
                                    маршрута до филиала назначения</label>
                                <div class="col-md-6">
                                    <input
                                        :class="{'is-invalid':$v.data.routeLengthToDestination.$error || errors.routeLengthToDestination}"
                                        autocomplete="routeLengthToDestination"
                                        autofocus
                                        class="form-control"
                                        id="routeLengthToDestination"
                                        name="routeLengthToDestination"
                                        type="number"
                                        min="0"
                                        v-model="data.routeLengthToDestination">

                                    <span class="invalid-feedback" role="alert"
                                          v-if="$v.data.routeLengthToDestination.$error || errors.routeLengthToDestination">
                                        <strong>Необходимо ввести длину маршрута в км</strong>
                                        <strong v-for="message in errors.routeLengthToDestination">{{message}}</strong>
                                    </span>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-md-4 col-form-label text-md-right" for="routeLengthWithCargoTo">Длина
                                    маршрута с грузом</label>
                                <div class="col-md-6">
                                    <input
                                        :class="{'is-invalid':$v.data.routeLengthWithCargoTo.$error || errors.routeLengthWithCargoTo}"
                                        autocomplete="routeLengthWithCargoTo"
                                        autofocus
                                        class="form-control"
                                        id="routeLengthWithCargoTo"
                                        name="routeLengthWithCargoTo"
                                        type="number"
                                        min="0"
                                        v-model="data.routeLengthWithCargoTo">

                                    <span class="invalid-feedback" role="alert"
                                          v-if="$v.data.routeLengthWithCargoTo.$error || errors.routeLengthWithCargoTo">
                                        <strong>Необходимо ввести длину маршрута в км</strong>
                                        <strong v-for="message in errors.routeLengthWithCargoTo">{{message}}</strong>
                                    </span>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-md-4 col-form-label text-md-right" for="cargoWeightTo">Вес груза
                                    (кг)</label>
                                <div class="col-md-6">
                                    <input
                                        :class="{'is-invalid':$v.data.cargoWeightTo.$error || errors.cargoWeightTo}"
                                        autocomplete="cargoWeightTo"
                                        autofocus
                                        class="form-control"
                                        id="cargoWeightTo"
                                        name="cargoWeightTo"
                                        type="number"
                                        min="0"
                                        v-model="data.cargoWeightTo">

                                    <span class="invalid-feedback" role="alert"
                                          v-if="$v.data.cargoWeightTo.$error || errors.cargoWeightTo">
                                        <strong>Необходимо ввести вес груза в кг</strong>
                                        <strong v-for="message in errors.cargoWeightTo">{{message}}</strong>
                                    </span>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-md-4 col-form-label text-md-right" for="trailerCargoWeightTo">Вес
                                    груза прицепа (кг)</label>
                                <div class="col-md-6">
                                    <input
                                        :class="{'is-invalid':$v.data.trailerCargoWeightTo.$error || errors.trailerCargoWeightTo}"
                                        autocomplete="trailerCargoWeightTo"
                                        autofocus
                                        class="form-control"
                                        id="trailerCargoWeightTo"
                                        name="trailerCargoWeightTo"
                                        type="number"
                                        min="0"
                                        v-model="data.trailerCargoWeightTo">

                                    <span class="invalid-feedback" role="alert"
                                          v-if="$v.data.trailerCargoWeightTo.$error || errors.trailerCargoWeightTo">
                                        <strong>Необходимо ввести вес груза прицепа в кг</strong>
                                        <strong v-for="message in errors.trailerCargoWeightTo">{{message}}</strong>
                                    </span>
                                </div>
                            </div>
                            <hr>
                            <div class="form-group row">
                                <label class="col-md-4 col-form-label text-md-right" for="routeLengthFromDestination">Длина
                                    обратного пути</label>
                                <div class="col-md-6">
                                    <input
                                        :class="{'is-invalid':$v.data.routeLengthFromDestination.$error || errors.routeLengthFromDestination}"
                                        autocomplete="routeLengthFromDestination"
                                        autofocus
                                        class="form-control"
                                        id="routeLengthFromDestination"
                                        name="routeLengthFromDestination"
                                        type="number"
                                        min="0"
                                        v-model="data.routeLengthFromDestination">

                                    <span class="invalid-feedback" role="alert"
                                          v-if="$v.data.routeLengthFromDestination.$error || errors.routeLengthFromDestination">
                                        <strong>Необходимо ввести длину маршрута в км</strong>
                                        <strong
                                            v-for="message in errors.routeLengthFromDestination">{{message}}</strong>
                                    </span>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-md-4 col-form-label text-md-right" for="cargoWeightFrom">Вес груза
                                    (кг)</label>
                                <div class="col-md-6">
                                    <input
                                        :class="{'is-invalid':$v.data.cargoWeightFrom.$error || errors.cargoWeightFrom}"
                                        autocomplete="cargoWeightFrom"
                                        autofocus
                                        class="form-control"
                                        id="cargoWeightFrom"
                                        name="cargoWeightFrom"
                                        type="number"
                                        min="0"
                                        v-model="data.cargoWeightFrom">

                                    <span class="invalid-feedback" role="alert"
                                          v-if="$v.data.cargoWeightFrom.$error || errors.cargoWeightFrom">
                                        <strong>Необходимо ввести вес груза в кг</strong>
                                        <strong v-for="message in errors.cargoWeightFrom">{{message}}</strong>
                                    </span>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-md-4 col-form-label text-md-right" for="trailerCargoWeightFrom">Вес
                                    груза прицепа (кг)</label>
                                <div class="col-md-6">
                                    <input
                                        :class="{'is-invalid':$v.data.trailerCargoWeightFrom.$error || errors.trailerCargoWeightFrom}"
                                        autocomplete="trailerCargoWeightFrom"
                                        autofocus
                                        class="form-control"
                                        id="trailerCargoWeightFrom"
                                        name="trailerCargoWeightFrom"
                                        type="number"
                                        min="0"
                                        v-model="data.trailerCargoWeightFrom">

                                    <span class="invalid-feedback" role="alert"
                                          v-if="$v.data.trailerCargoWeightFrom.$error || errors.trailerCargoWeightFrom">
                                        <strong>Необходимо ввести вес груза прицепа в кг</strong>
                                        <strong v-for="message in errors.trailerCargoWeightFrom">{{message}}</strong>
                                    </span>
                                </div>
                            </div>
                            <hr>
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
            </div>
        </div>

    </div>


</template>

<script>
    import {required, maxLength, not, sameAs} from 'vuelidate/lib/validators/';

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
                        number: null
                    },
                    driver: null,
                    departure_branch: null,
                    destination_branch: null,
                    departureDate: null,
                    returnDate: null,
                    // emptyToDestination: null,
                    // emptyFromDestination: null,
                    routeLengthToDestination: 0,
                    routeLengthWithCargoTo: 0,
                    routeLengthFromDestination: 0,
                    cargoWeightTo: 0,
                    trailerCargoWeightTo: 0,
                    cargoWeightFrom: 0,
                    trailerCargoWeightFrom: 0,
                    mileageBefore: 0
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
                    returnDate: null,
                    hasTrailer: null,
                    departure_branch: null,
                    destination_branch: null,
                    // emptyToDestination: null,
                    // emptyFromDestination: null,
                    routeLengthToDestination: null,
                    routeLengthWithCargoTo: null,
                    routeLengthFromDestination: null,
                    cargoWeightTo: null,
                    trailerCargoWeightTo: null,
                    cargoWeightFrom: null,
                    trailerCargoWeightFrom: null,
                    mileageBefore: null
                }
            }
        },
        methods: {
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
                        hasTrailer: this.data.hasTrailer === true,
                        // emptyToDestination: this.data.emptyToDestination === true,
                        // emptyFromDestination: this.data.emptyFromDestination === true,
                        departure_branch_id: this.data.departure_branch.id,
                        destination_branch_id: this.data.destination_branch.id,
                        id: this.data.id,
                        routeLengthToDestination: this.data.routeLengthToDestination,
                        routeLengthWithCargoTo: this.data.routeLengthWithCargoTo,
                        routeLengthFromDestination: this.data.routeLengthFromDestination,
                        cargoWeightTo: this.data.cargoWeightTo,
                        trailerCargoWeightTo: this.data.trailerCargoWeightTo,
                        cargoWeightFrom: this.data.cargoWeightFrom,
                        trailerCargoWeightFrom: this.data.trailerCargoWeightFrom,
                        mileageBefore: this.data.mileageBefore
                    };

                    let response;
                    if (this.isEditMode)
                        response = await axios.patch('/trips/' + data.id, data);
                    else
                        response = await axios.post('/trips', data);
                    window.location = getBaseUrl() + '/trips/' + response.data.id;
                } catch (e) {
                    if (e.response.status === 422) {
                        this.errors.code = e.response.data.errors.code;
                        this.errors.car = e.response.data.errors.carId;
                        this.errors.driver = e.response.data.errors.driverId;
                        this.errors.departureDate = e.response.data.errors.departureDate;
                        this.errors.returnDate = e.response.data.errors.returnDate;
                        this.errors.hasTrailer = e.response.data.errors.hasTrailer;
                        this.errors.departure_branch = e.response.data.errors.departure_branch_id;
                        this.errors.destination_branch = e.response.data.errors.destination_branch_id;
                        // this.errors.emptyToDestination = e.response.data.errors.emptyToDestination;
                        // this.errors.emptyFromDestination = e.response.data.errors.emptyFromDestination;
                        this.errors.routeLengthToDestination = e.response.data.errors.routeLengthToDestination;
                        this.errors.routeLengthWithCargoTo = this.data.routeLengthWithCargoTo;
                        this.errors.routeLengthFromDestination = e.response.data.errors.routeLengthFromDestination;
                        this.errors.cargoWeightTo = this.data.cargoWeightTo;
                        this.errors.trailerCargoWeightTo = this.data.trailerCargoWeightTo;
                        this.errors.cargoWeightFrom = this.data.cargoWeightFrom;
                        this.errors.trailerCargoWeightFrom = this.data.trailerCargoWeightFrom;
                        this.errors.mileageBefore = this.data.mileageBefore;
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
                },
                departure_branch: {
                    required
                },
                destination_branch: {
                    required,
                    isNotSameAsDepartureBranch: not(sameAs('departure_branch'))
                },
                routeLengthToDestination: {
                    required
                },
                routeLengthWithCargoTo: {
                    required
                },
                routeLengthFromDestination: {
                    required
                },
                cargoWeightTo: {
                    required
                },
                trailerCargoWeightTo: {
                    required
                },
                cargoWeightFrom: {
                    required
                },
                trailerCargoWeightFrom: {
                    required
                },
                mileageBefore: {
                    required
                }
            }
        }
    }

</script>
