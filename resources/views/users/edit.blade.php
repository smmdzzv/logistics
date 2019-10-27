@extends('layouts.app')

@section('content')
    @isset($user)
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card shadow">
                    <div class="card-header">Редактирование профиля</div>

                    <div class="card-body">

                        <form method="POST" action="{{ route('users.update', [$user->id]) }}">
                            @csrf
                            @method('PATCH')

                            <div class="form-group row">
                                <label for="name" class="col-md-4 col-form-label text-md-right">ФИО</label>

                                <div class="col-md-6">
                                    <input id="name" type="text" class="form-control @error('name') is-invalid @enderror" name="name" value="{{ old('name') ?? $user->name }}" autocomplete="name" autofocus>

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
                                    <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') ?? $user->email}}" required autocomplete="email">

                                    @error('email')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="code" class="col-md-4 col-form-label text-md-right">Код</label>

                                <div class="col-md-6">
                                    <input id="code" type="text" class="form-control @error('code') is-invalid @enderror" name="code" value="{{ old('code') ?? $user->code }}" required autocomplete="code" autofocus>

                                    @error('code')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="phone" class="col-md-4 col-form-label text-md-right">Телефон</label>

                                <div class="col-md-6">
                                    <input id="phone" type="text" class="form-control @error('name') is-invalid @enderror" name="phone" value="{{ old('phone') ?? $user->phone }}" autocomplete="phone" autofocus>

                                    @error('phone')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="password" class="col-md-4 col-form-label text-md-right">Пароль</label>

                                <div class="col-md-6">
                                    <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" autocomplete="new-password">

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
                                    <input id="password-confirm" type="password" class="form-control" name="password_confirmation" autocomplete="new-password">
                                </div>
                            </div>

                            @auth
                                <div class="form-group row">
                                    <label for="position" class="col-md-4 col-form-label text-md-right">Должность</label>

                                    <div class="col-md-6">
                                        <input id="position"
                                               type="text"
                                               class="form-control @error('position') is-invalid @enderror"
                                               name="position"
                                               @isset($user->position->name)
                                               value="{{ old('position') ?? $user->position->name}}"
                                               @else
                                               value="{{ old('position')}}"
                                               @endisset
                                               autocomplete="position-name">

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
                                                    autocomplete="branch" required>
                                                <option value="" @if(!$user->branch) selected @endif  disabled>--Выберите филиал--</option>
                                                @foreach($branches as $branch)
                                                    <option value="{{$branch->id}}" @if($user->branch && $user->branch->id === $branch->id) selected @endif>{{$branch->name}}</option>
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
                                                    required>
                                                @foreach($roles as $role)
                                                    <option value="{{(String)$role->id}}"
                                                    @if(array_search($role->id, array_column($user->roles->toArray(), 'id')) !== false)
                                                        selected="selected"
                                                    @endif
                                                    >{{$role->title}}</option>
                                                @endforeach
                                            </select>

                                            @error('roles')
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
                                        {{ __('Сохранить') }}
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endisset
@endsection
