@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-header">Dashboard</div>

                <div class="card-body">
                    <ul>
                        <li><a href="/settings">Настройки</a></li>
                        <li><a href="/user/create">Регистрация пользователей</a></li>
                        <li><a href="/order/create">Оформить заказ</a></li>
                        <li><a href="/tariff/create">Редактирование тарифов</a></li>
                        <li><a href="/tariff-price-history/create">Редактирование тарифных планов</a></li>
                        <li><a href="/tariff-price-history">Список историй тарифных планов</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
