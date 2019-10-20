@extends('layouts.app')

@section('content')
    <div class="container" xmlns:v-slot="http://www.w3.org/1999/XSL/Transform">
        <div class="row justify-content-center">
            <div class="col-sm-10 col-md-8 col-lg-6 col-xl-4 mb-4">
                <div class="card shadow">
                    <div class="card-header">
                        <div class="row">
                            <div class="col-6">Детали рейса</div>
                            <div class="col-6 text-right">
                                @if(auth()->user()->hasRole('admin'))
                                <a  href="{{route('trip.edit-loaded', $trip)}}">
                                    <img class="icon-btn-sm" src="/svg/car-loading.svg">
                                </a>
                                <a class="pl-3"  href="#">
                                    <img class="icon-btn-sm" src="/svg/car-unloading.svg">
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
                        @if($trip->hasTrailer)
                            <p class="text-success">С прицепом</p>
                        @else
                            <p class="text-danger">Без прицепа</p>
                        @endif
                        <p>Дата отправления: <span class="font-weight-bold">{{$trip->departureDate}}</span></p>
                        <p>Дата возвращения: <span class="font-weight-bold">{{$trip->returnDate}}</span></p>
                        <p>Факт. дата отправления: </p>
                        <p>Факт. дата возвращения: </p>
                    </div>
                </div>
            </div>
            <div class="col-12 col-lg-8">
                <stored-table class="shadow" :load-data="false" :provided-items="{{$trip->storedItems}}"
                              :selectable="false">
                    <template v-slot:header>
                        <div class="card-header text-right">
                            @if($trip->isEditable())
                                <a href="{{route('trip.edit-items', $trip->id)}}">Изменить список товаров</a>
                            @endif
                        </div>
                    </template>
                </stored-table>
            </div>
        </div>
    </div>
@endsection
