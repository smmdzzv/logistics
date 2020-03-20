@extends('layouts.app')

@section('content')
    <div class="container col-12 col-md-11 col-lg-9">
        <div class="card">
            <div class="card-header">
                <div class="md-6">Обзор машины</div>
            </div>
            <div class="card-body">
                <!--Car main info-->
                <div class=" mt-3">
                    <div class="row col-10 offset-1 align-items-baseline">
                        <div class="mr-auto">
                            <span>Номер машины: {{$car->number}}</span>
                        </div>
                        <div>
                            <a class="btn btn-link pl-0" href="{{route('cars.edit', $car)}}">Редактировать</a>
                            <a href="#" class="btn btn-link" onclick="destroyCar()">Удалить</a>
                        </div>
                    </div>
                    <div class="row">
                        <div class="jumbotron col-10 offset-1 mt-2">
                            <div class="row text-center">
                                <h5 class="col-12 mb-4">Машина</h5>
                            </div>
                            <p>Серийный номер: {{$car->serial}}</p>
                            <p>Остаток топлива: {{$car->fuelAmount}} л</p>
                            <p>Длина: {{$car->length}} | Ширина: {{$car->width}} | Высота: {{$car->height}} </p>
                            <p>Грузоподъемность: {{$car->maxWeight}} кг | Кубатура: {{$car->maxCubage}} м<sup>3</sup>
                            </p>
                            @if($car->trailerNumber)
                                <hr>
                                <div class="row text-center">
                                    <h5 class="col-12 my-4">Прицеп</h5>
                                </div>
                                <p>Номер: {{$car->trailerNumber}}</p>
                                <p>Грузоподъемность: {{$car->trailerMaxWeight}} |
                                    Кубатура:{{$car->trailerMaxCubage}} </p>
                            @endif
                        </div>
                    </div>

                </div>
                <!--Fuel consumption info-->
                <hr>
                <div>
                    <div class="row col-10 offset-1 align-items-baseline">
                        <div class="mr-auto">
                            <h6>Расход топлива</h6>
                        </div>

                        <div class=" ">
                            <a class="btn btn-link pl-0" href="{{route('car-fuel-consumption.edit', $car)}}">Редактировать</a>
                        </div>
                    </div>
                    @if($car->toChinaConsumption)
                        <div class="row">
                            <div class="jumbotron col-10 offset-1 mt-2">
                                <div class="row text-center">
                                    <h5 class="col-12 mb-4">В Китай</h5>
                                </div>
                                <p> Расход для пустой машины: {{$car->toChinaConsumption->forEmpty}} л</p>
                                <p> Расход для загруженной машины: {{$car->toChinaConsumption->forLoaded}} л</p>
                                <p> Расход для пустой машины c прицепом: {{$car->toChinaConsumption->forEmptyTrailer}}
                                    л</p>
                                <p> Расход для загруженной машины с
                                    прицепом: {{$car->toChinaConsumption->forLoadedTrailer}} л</p>
                            </div>
                        </div>
                    @endif
                    @if($car->fromChinaConsumption)
                        <div class="row">
                            <div class="jumbotron col-10 offset-1 mt-2">
                                <div class="row text-center">
                                    <h5 class="col-12 mb-4">Из Китая</h5>
                                </div>
                                <p> Расход для пустой машины: {{$car->fromChinaConsumption->forEmpty}} л</p>
                                <p> Расход для загруженной машины: {{$car->fromChinaConsumption->forLoaded}} л</p>
                                <p> Расход для пустой машины c прицепом: {{$car->fromChinaConsumption->forEmptyTrailer}}
                                    л</p>
                                <p> Расход для загруженной машины с
                                    прицепом: {{$car->fromChinaConsumption->forLoadedTrailer}} л</p>
                            </div>
                        </div>
                    @endif

                </div>
            </div>
        </div>
    </div>
@endsection
<script>
    async function destroyCar() {
        let confirm = await window.app.$bvModal.msgBoxConfirm(`Вы уверены что хотите удалить машину {{$car->number}}?`,
            {
                centered: true,
                okTitle: 'Да',
                cancelTitle: 'Отменить',
                footerClass: 'border-0',
                title: 'Подтверждение удаления'
            });

        if (confirm) {
            try {
                let action = `/cars/{{$car->id}}`;
                const response = await axios.delete(action);
                location = '{{route('cars.index')}}';
            } catch (e) {
                await window.app.$bvModal.msgBoxOk(`Не удалось удалить машину  {{$car->number}}. Перезагрузите страницу и попробуйте еще раз`, {
                    centered: true,
                    okTitle: 'Закрыть',
                    footerClass: 'border-0',
                    title: 'Сообщение об ошибке'
                });
            }
        }
    }
</script>
