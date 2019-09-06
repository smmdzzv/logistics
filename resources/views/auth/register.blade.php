@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Register') }}</div>

                <div class="card-body">
                    @auth
                        <form method="POST" action="{{ route('register-manual') }}">
                    @endauth
                    @guest
                        <form method="POST" action="{{ route('register') }}">
                    @endguest
                        @csrf

                        <div class="form-group row">
                            <label for="name" class="col-md-4 col-form-label text-md-right">ФИО</label>

                            <div class="col-md-6">
                                <input id="name" type="text" class="form-control @error('name') is-invalid @enderror" name="name" value="{{ old('name') }}" required autocomplete="name" autofocus>

                                @error('name')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="email" class="col-md-4 col-form-label text-md-right">Электронная почта</label>

                            <div class="col-md-6">
                                <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email">

                                @error('email')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="password" class="col-md-4 col-form-label text-md-right">Пароль</label>

                            <div class="col-md-6">
                                <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="new-password">

                                @error('password')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="password-confirm" class="col-md-4 col-form-label text-md-right">Подтвердите пароль</label>

                            <div class="col-md-6">
                                <input id="password-confirm" type="password" class="form-control" name="password_confirmation" required autocomplete="new-password">
                            </div>
                        </div>

                        @auth
                            <div class="form-group row">
                                <label for="position-name" class="col-md-4 col-form-label text-md-right">Должность</label>

                                <div class="col-md-6">
                                    <input id="position-name"
                                           type="text"
                                           class="form-control @error('position') is-invalid @enderror"
                                           name="position-name"
                                           value="{{ old('position') }}"
                                           autocomplete="position-name" required>

                                    @error('position')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>
                            </div>
                        @isset($branches)
                            <div class="form-group row">
                                <label for="branch" class="col-md-4 col-form-label text-md-right">Филиал</label>

                                <div class="col-md-6">
                                    <select id="branch"
                                           type="text"
                                           class="form-control custom-select @error('branch') is-invalid @enderror"
                                           name="branch"
                                           value="{{ old('branch') }}"
                                           autocomplete="branch" required>

                                        @foreach($branches as $branch)
                                            <option value="{{$branch->id}}">{{$branch->name}}</option>
                                        @endforeach
                                    </select>
                                    @error('position')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>
                            </div>
                            @endisset

                            @isset($roles)
                                <div class="form-group row">
                                    <label for="roles" class="col-md-4 col-form-label text-md-right">Роли</label>

                                    <div class="col-md-6">
                                        <select id="roles"
                                                class="form-control custom-select @error('roles') is-invalid @enderror"
                                                name="roles[]"
                                                multiple="multiple"
                                                value="{{ old('roles') }}"
                                                autocomplete="roles" required>

                                            @foreach($roles as $role)
                                                <option value="{{$role->id}}">{{$role->title}}</option>
                                            @endforeach
                                        </select>
                                        @error('position')
                                        <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                        @enderror
                                    </div>
                                </div>
                            @endisset
                        @endauth

                        <div class="form-group row mb-0">
                            <div class="col-md-6 offset-md-4">
                                <button type="submit" class="btn btn-primary">
                                    {{ __('Регистрация') }}
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
