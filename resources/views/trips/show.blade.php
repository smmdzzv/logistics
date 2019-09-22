@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center justify-content-md-end">
            <div class="col-sm-8 col-md-6 col-lg-5 col-xl-4">
                <div class="card shadow">
                    <div class="card-header">
                        <div class="row">
                            <div class="col-6">Детали рейса</div>
                            <div class="col-6 text-right"><a href="/trips/{{$trip->id}}/edit">Изменить</a></div>
                        </div>
                    </div>
                    <div class="card-body">
                        <p>Номер рейса: <span class="font-weight-bold">{{$trip->code}}</span></p>
                        <p>Водитель: <span class="font-weight-bold">{{$trip->driver->name}}</span></p>
                        <p>Машина: <span class="font-weight-bold">{{$trip->car->number}}</span></p>
                        <p>Дата отправления: <span class="font-weight-bold">{{$trip->departureDate}}</span></p>
                        <p>Дата возвращения: <span class="font-weight-bold">{{$trip->returnDate}}</span></p>
                        <p>Факт. дата отправления: </p>
                        <p>Факт. дата возвращения: </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
