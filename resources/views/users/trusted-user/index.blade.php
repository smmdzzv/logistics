@extends('layouts.app')

@section('content')

    <div class="container">
        <div class="card">
            <div class="card-header">
                Доверенные пользователи
            </div>
            <div class="card-body">
                <div class="row">
                    <a class="ml-auto btn btn-primary"
                       href="{{route('trusted-user.create')}}">
                        Добавить
                    </a>
                </div>
                <div class="row">
                    @foreach($trustedUsers as $user)

                    @endforeach
                </div>
            </div>
        </div>
    </div>

@endsection
