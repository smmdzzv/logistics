@extends('layouts.app')

@section('content')
    <div class="row p-4 justify-content-center">
        <div class="col-sm-10 col-md-8 col-lg-4 mb-4">
            <div class="card shadow">
                <div class="card-header">
                    <div class="row">
                        <div class="col-6">Детали рейса</div>
                        <div class="col-6 text-right">
                            @if(auth()->user()->hasAnyRole(['admin', 'manager', 'storekeeper']))
                                <a href="{{route('trip.edit-loaded', $trip)}}">
                                    <img class="icon-btn-sm" src="/svg/car-loading.svg">
                                </a>
                                <a class="pl-3" href="{{route('trip.edit-unloaded', $trip)}}">
                                    <img class="icon-btn-sm" src="/svg/car-unloading.svg">
                                </a>
                                <a class="pl-3" href="{{route('trip.change-items-trip', $trip)}}">
                                    <img class="icon-btn-sm" src="/svg/car-sending.svg">
                                </a>
                            @endif
                            @if($trip->isEditable())
                                <a class="pl-3" href="/trips/{{$trip->id}}/edit">
                                    <img class="icon-btn-sm" src="/svg/edit.svg">
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <p>Номер рейса: <span class="font-weight-bold">{{$trip->code}}</span></p>
                    <p>Водитель: <span class="font-weight-bold">{{$trip->driver->name}}</span></p>
                    <p>Машина: <span class="font-weight-bold">{{$trip->car->number}}</span></p>
                    <p>Статус:
                        @switch($trip->status)
                            @case('created')
                            <span>В ожидании загрузки</span>
                            @break
                            @case('active')
                            <span class="text-success">В пути</span>
                            @break
                            @case('finished')
                            <span class="text-danger">Завершен</span>
                            @break
                        @endswitch
                    </p>
                    <p> Прицеп:
                        @if($trip->hasTrailer)
                            <span class="text-success">С прицепом</span>
                        @else
                            <span class="text-danger">Без прицепа</span>
                        @endif
                    </p>

                    <p>Длина маршрута до пункта назначения: <span class="font-weight-bold">{{$trip->routeLengthToDestination}} км</span>
                    </p>
                    <p>Длина обратного пути: <span
                            class="font-weight-bold">{{$trip->routeLengthFromDestination}} км</span></p>
                    <p>Дата отправления: <span class="font-weight-bold"
                                               v-luxon="{ value: '{{$trip->departureDate}}',  clientFormat: 'dd-MM-yyyy'}"></span>
                    </p>
                    <p>Дата возвращения: <span class="font-weight-bold"
                                               v-luxon="{ value: '{{$trip->returnDate}}',  clientFormat: 'dd-MM-yyyy'}"></span>
                    </p>
                    @if($trip->departureAt)
                        <p>Факт. дата отправления: <span class="font-weight-bold"
                                                         v-luxon="{ value: '{{$trip->departureAt}}' }"/></p>
                    @endif
                    @if($trip->returnedAt)
                        <p>Факт. дата возвращения: <span class="font-weight-bold"
                                                         v-luxon="{ value: '{{$trip->returnedAt}}' }"></span></p>
                    @endif

                    <p>Планируемый расход топлива: <span class="font-weight-bold">
                            {{$calculatedConsumptionTo + $calculatedConsumptionFrom}}
                            (до {{$calculatedConsumptionTo}}, от {{$calculatedConsumptionFrom}}) л</span>
                    </p>

                    <form method="post" action="{{route('trip.status', $trip)}}">
                        @csrf
                        <div class="form-group">
                            <label>Фактический расход топлива:</label>
                            <input class="form-control is-valid"
                                   type='number'
                                   name="consumption"
                                   step="0.01"
                                   @if($trip->status !== 'finished')
                                   value="{{$trip->totalFuelConsumption > 0 ? $trip->totalFuelConsumption : $calculatedConsumptionTo + $calculatedConsumptionFrom}}"
                                   @else
                                   disabled
                                   value="{{$trip->totalFuelConsumption}}"
                                @endif>
                        </div>
                        @php $distance = $trip->mileageAfter - $trip->mileageBefore @endphp
                        <div class="form-group">
                            <label>Пробег машины в конце рейса</label>
                            <input class="form-control @if($distance > 0) is-valid @else is-invalid @endif"
                                   type='number'
                                   name="mileageAfter"
                                   step="1"
                                   @if($trip->status === 'finished')
                                   disabled
                                   @endif
                                   value="{{$trip->mileageAfter}}">
                        </div>
                        <p>Фактический километраж: {{$trip->mileageAfter}} - {{ $trip->mileageBefore }}= {{$distance}}
                            км</p>

                        <input type="hidden" name="status"
                               @switch($trip->status)
                               @case('created')
                               value="active"
                               @break
                               @case('active')
                               value="finished"
                            @break
                            @endswitch
                        >

                        @switch($trip->status)
                            @case('created')
                            <button class="btn btn-primary">Начать рейс</button>
                            @break
                            @case('active')
                            <button class="btn btn-success">Завершить рейс</button>
                            @break
                        @endswitch
                    </form>
                </div>
            </div>
        </div>
        <div class="col-12 col-lg-8">
            <sortable-stored-table class="shadow" :stored-items="{{$trip->storedItems}}">
                <template v-slot:header>
                    <div class="text-right pl-3">
                        @if($trip->isEditable())
                            <a href="{{route('trip.edit-items', $trip->id)}}">Изменить список товаров</a>
                        @endif
                    </div>
                </template>
            </sortable-stored-table>
        </div>
    </div>
@endsection

<script>
    // document.addEventListener('DOMContentLoaded', function () {
    //     $('#endTrip').click(function (event) {
    //         event.preventDefault();
    //         alert(2);
    //     })
    // });

</script>
