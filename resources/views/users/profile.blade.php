@extends('layouts.app')

@section('content')
    {{--    <profile :user="{{$user}}"></profile>--}}

    <div class="p-3">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Профиль пользователя</h5>
            </div>
            <div class="card-body">
                <div class="row mt-2">
                    <h4 class="card-title col-sm-6">{{$user->name}} {{$user->code}}</h4>
                </div>
                <h6 class="text-muted card-subtitle mb-5">{{$user->email}}  {{$user->phone}}</h6>
                <div class="jumbotron">
                    <h4>Состояние счета</h4>
                    @foreach($user->accounts as $account)
                        Баланс: {{$account->balance}} {{$account->currency->isoName}}
                    @endforeach
                </div>
                <div class="jumbotron">
                    <h4>Активные заказы</h4>
                    <orders-table :provided-orders="{{$user->activeOrders}}" :action="action"></orders-table>
                </div>
            </div>
        </div>
    </div>
@endsection
