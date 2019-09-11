@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-header">Dashboard</div>

                <div class="card-body">
                    <ul>
                        <hr>
                            <li><a href="/user/create">Регистрация пользователей</a></li>
                            <li><a href="/clients">Список клиентов</a></li>
                            <li><a href="/employees">Список сотрудников</a></li>
                        <hr>
                        <li><a href="/settings">Настройки</a></li>
                        <hr>
                        <li><a href="/order/create">Оформить заказ</a></li>
                        <li><a href="/order">Список заказов</a></li>
                        <li><a href="/order/1">Посмотреть заказ с id=1</a></li>
                        <hr>
                        <li><a href="/tariff/create">Редактирование тарифов</a></li>
                        <li><a href="/tariff-price-history/create">Редактирование тарифных планов</a></li>
                        <li><a href="/tariff-price-history">Список историй тарифных планов</a></li>
                        <hr>
                        <li><a href="{{route('stored.index')}}">Список принятых товаров</a></li>
                        <hr>
                        <li><a href="{{route('car.create')}}">Добавить машину</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
