@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="card ">
            <div class="card-header">
                <div class="md-6">Обзор машины</div>
            </div>
            <div class="card-body">
                <!--Car main info-->
                <div class="container">
                    <div class="row">
                        <div class="col-12 col-md-6">
                            <h5>Номер машины: {{$car->number}}</h5>
                        </div>
                        <div class="row col-md-6">
                            <a class="btn btn-link ml-md-auto" href="{{route('cars.edit', $car)}}">Редактировать</a>
                            <button class="btn btn-link" onclick="destroyCar()">Удалить</button>
                        </div>
                    </div>
                    <div class="row">
                        <div class="jumbotron col-10 offset-1 mt-4">
                            <h5 class="mb-3">Машина</h5>
                            <p>Серийный номер: {{$car->serial}}</p>
                            <p>Длина: {{$car->length}} | Ширина: {{$car->width}} | Высота: {{$car->height}} </p>
                            <p>Грузоподъемность: {{$car->maxWeight}} кг | Кубатура: {{$car->maxCubage}} м<sup>3</sup>
                            </p>
                            <hr>
                            @if($car->trailerNumber)
                                <h5 class="mb-3">Прицеп</h5>
                                <p>Номер: {{$car->trailerNumber}}</p>
                                <p>Грузоподъемность: {{$car->trailerMaxWeight}} |
                                    Кубатура:{{$car->trailerMaxCubage}} </p>
                            @endif
                        </div>
                    </div>

                </div>
                <!--Fuel consumption info-->
                <hr>
                <div class="container">
                    <div class="row">
                        <div class="col-12 col-md-6">
                            <h5>Расход топлива</h5>
                        </div>
                        <div class="row col-md-6">
                            <a class="btn btn-link ml-md-auto" href="{{route('car-fuel-consumption.edit', $car)}}">Редактировать</a>
                        </div>
                    </div>
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
