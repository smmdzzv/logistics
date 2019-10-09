<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Scripts -->
    <script src="{{ asset('js/app.js') }}" defer></script>
    <script src="{{ asset('js/main.js') }}" defer></script>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet">

    <!-- Styles -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
</head>
<body>
<div id="app">
    <nav class="navbar navbar-expand-md navbar-light bg-white shadow-sm sticky-top">
        <div class="container">
            <a class="navbar-brand" href="{{ url('/home') }}">
                {{ config('app.name', 'Laravel') }}
            </a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent"
                    aria-controls="navbarSupportedContent" aria-expanded="false"
                    aria-label="{{ __('Toggle navigation') }}">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarSupportedContent">
            @auth
                <!-- Left Side Of Navbar -->
                    <ul class="navbar-nav ml-4 mr-auto">
                        <!--Orders-->
                        <li class="nav-item dropdown">
                            <a id="ordersMenuDropdown" class="nav-link dropdown-toggle" href="#" role="button"
                               data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                Заказы <span class="caret"></span>
                            </a>
                            <div class="dropdown-menu" aria-labelledby="ordersMenuDropdown">
                                <a class="dropdown-item" href="{{ route('orders.create') }}">Оформить заказ</a>
                                <a class="dropdown-item" href="{{route('orders.index')}}">Список заказов</a>
                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item" href="{{route('stored.index')}}">Принятые товары</a>
                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item" href="{{route('tariffs.index')}}">Управление тарифами</a>
                                <div class="dropdown-divider"></div>
                                <h6 class="dropdown-header">Расценка тарифов</h6>
                                <a class="dropdown-item" href="{{route('tariff-price-histories.create')}}">Обновить
                                    расценки</a>
                                <a class="dropdown-item" href="{{route('tariff-price-histories.index')}}">История цен</a>
                                <div class="dropdown-divider"></div>
                                <h6 class="dropdown-header">Наименования</h6>
                                <a class="dropdown-item" href="{{route('items.create')}}">Добавить наименование</a>
                                <a class="dropdown-item" href="{{route('items.index')}}">Список  наименований</a>
                            </div>
                        </li>
                        <!--Till-->
                        <li class="nav-item dropdown">
                            <a id="tillMenuDropdown" class="nav-link dropdown-toggle" href="#" role="button"
                               data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                Касса <span class="caret"></span>
                            </a>
                            <div class="dropdown-menu" aria-labelledby="tillMenuDropdown">
                                <h6 class="dropdown-header">Провести платеж</h6>
                                <a class="dropdown-item" href="#">Приход</a>
                                <a class="dropdown-item" href="#">Расход</a>
                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item" href="#">Сформировать отчет</a>
                                <div class="dropdown-divider"></div>
                                <h6 class="dropdown-header">Статьи прихода/расхода</h6>
                                <a class="dropdown-item" href="#">Добавить</a>
                                <a class="dropdown-item" href="#">Список</a>
                                <div class="dropdown-divider"></div>
                                <h6 class="dropdown-header">Валюта</h6>
                                <a class="dropdown-item" href="#">Добавить</a>
                                <a class="dropdown-item" href="#">Список</a>
                            </div>
                        </li>
                        <!--Users-->
                        <li class="nav-item dropdown">
                            <a id="usersMenuDropdown" class="nav-link dropdown-toggle" href="#" role="button"
                               data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                Пользователи <span class="caret"></span>
                            </a>
                            <div class="dropdown-menu" aria-labelledby="usersMenuDropdown">
                                <a class="dropdown-item" href="{{route("users.create")}}">Регистрация</a>
                                <a class="dropdown-item" href="{{route("users.index")}}">Список</a>
                            </div>
                        </li>
                        <!--Trips-->
                        <li class="nav-item dropdown">
                            <a id="tripsMenuDropdown" class="nav-link dropdown-toggle" href="#" role="button"
                               data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                Рейсы <span class="caret"></span>
                            </a>
                            <div class="dropdown-menu" aria-labelledby="tripsMenuDropdown">
                                <a class="dropdown-item" href="#">Создать</a>
                                <a class="dropdown-item" href="#">Список</a>
                                <div class="dropdown-divider"></div>
                                <h6 class="dropdown-header">Машины</h6>
                                <a class="dropdown-item" href="#">Добавить</a>
                                <a class="dropdown-item" href="#">Список</a>
                            </div>
                        </li>
                        <!--Branches-->
                        <li class="nav-item dropdown">
                            <a id="usersMenuDropdown" class="nav-link dropdown-toggle" href="#" role="button"
                               data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                Филиалы <span class="caret"></span>
                            </a>
                            <div class="dropdown-menu" aria-labelledby="usersMenuDropdown">
                                <a class="dropdown-item" href="{{route('branches.index')}}">Управлять</a>
                            </div>
                        </li>
                    </ul>
            @endauth
            <!-- Right Side Of Navbar -->
                <ul class="navbar-nav ml-auto">
                    <!-- Authentication Links -->
                    @guest
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('login') }}">Вход</a>
                        </li>
                        @if (Route::has('register'))
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('register') }}">Регистрация</a>
                            </li>
                        @endif
                    @else
                        <li class="nav-item dropdown">
                            <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button"
                               data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                {{ Auth::user()->name }} <span class="caret"></span>
                            </a>

                            <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
                                <a class="dropdown-item" href="{{ route('logout') }}"
                                   onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();">
                                    Выйти из системы
                                </a>

                                <form id="logout-form" action="{{ route('logout') }}" method="POST"
                                      style="display: none;">
                                    @csrf
                                </form>
                            </div>
                        </li>
                    @endguest
                </ul>
            </div>
        </div>
    </nav>

    <main class="py-4">
        @yield('content')
    </main>
    <b-modal
        no-close-on-esc
        no-close-on-backdrop
        hide-footer
        hide-header
        centered
        content-class="bg-transparent border-0"
        id="busyModal">
        <div class="d-block text-center">
            <b-spinner variant="light" label="Busy" style="width: 6rem; height: 6rem"/>
        </div>
    </b-modal>
</div>
</body>
</html>
