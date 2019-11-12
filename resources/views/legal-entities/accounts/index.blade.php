@extends('layouts.app')

@section('content')
    <div class="container col-12">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        Состояние счетов Дуоб
                    </div>
                    <div class="card-body">
                        @foreach($legalEntity->accounts as $account)
                            <div class="jumbotron">
                                <p>Баланс: {{$account->balance}}</p>
                                <p>Валюта: {{$account->currency->name . ' ' . $account->currency->isoName}}</p>
                                <p>Описание: {{$account->description}}</p>
                            </div>

                        @endforeach
                    </div>
                </div>
            </div>

        </div>
    </div>
@endsection
