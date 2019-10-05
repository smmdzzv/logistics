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
                            <li><a href="/users/create">Регистрация пользователей</a></li>
                            <li><a href="/concrete/client/index">Список клиентов</a></li>
                            <li><a href="/concrete/driver/index">Список водителей</a></li>
                        <hr>
                        <li><a href="/settings">Настройки</a></li>
                        <hr>
                        <li><a href="/orders/create">Оформить заказ</a></li>
                        <li><a href="/orders">Список заказов</a></li>
                        <li><a href="/orders/1">Посмотреть заказ с id=1</a></li>
                        <hr>
                        <li><a href="/tariff/create">Редактирование тарифов</a></li>
                        <li><a href="{{route('tariff-price-histories.create')}}">Редактирование тарифных планов</a></li>
                        <li><a href="{{route('tariff-price-histories.index')}}">Список историй тарифных планов</a></li>
                        <hr>
                        <li><a href="{{route('stored.index')}}">Список принятых товаров</a></li>
                        <hr>
                        <li><a href="{{route('cars.create')}}">Добавить машину</a></li>
                        <hr>
                        <li><a href="{{route('branches.index')}}">Управлять филиалами</a></li>
                        <hr>
                        <li><a href="{{route('trips.create')}}">Создать рейс</a></li>
                        <li><a href="/trips/">Список рейсов</a></li>
                        <hr>
                        <li><a href="{{route('incoming-payments.create')}}">Принять платеж</a></li>
                        <hr>
                        <li><a href="{{route('payment-items.index')}}">Статьи доходов/расходов</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
