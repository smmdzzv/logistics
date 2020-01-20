@extends('layouts.app')

@section('content')
    <div class="row p-4 justify-content-center" xmlns:v-slot="http://www.w3.org/1999/XSL/Transform">
        <div class="col-sm-10 col-md-8 col-lg-4 mb-4">
            <div class="card shadow">
                <div class="card-header">
                    <div class="row">
                        <div class="col-6">Детали рейса</div>
                        <div class="col-6 text-right">
                            @if(auth()->user()->hasRole('admin'))
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

                    <p>Длина маршрута до пункта назначения: <span class="font-weight-bold">{{$trip->routeLengthToDestination}} км</span></p>
                    <p>Длина обратного пути: <span class="font-weight-bold">{{$trip->routeLengthFromDestination}} км</span></p>
                    <p>Планируемый расход топлива: <span class="font-weight-bold">{{$calculatedConsumptionTo + $calculatedConsumptionFrom}} (до {{$calculatedConsumptionTo}}, от {{$calculatedConsumptionFrom}}) л</span></p>
                    <p>Дата отправления: <span class="font-weight-bold">{{$trip->departureDate}}</span></p>
                    <p>Дата возвращения: <span class="font-weight-bold">{{$trip->returnDate}}</span></p>
                    <p>Факт. дата отправления: <span class="font-weight-bold">{{$trip->departureAt}}</span></p>
                    <p>Факт. дата возвращения: <span class="font-weight-bold">{{$trip->returnedAt}}</span></p>

                    <form method="post" action="{{route('trip.status', $trip)}}">
                        @csrf
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
