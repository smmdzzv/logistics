@extends('layouts.app')

@section('content')
    <div class="container ">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card shadow">
                    <div class="card-header">Добавить валюту</div>

                    <div class="card-body">

                        <form method="POST" action="{{ route('currencies.store') }}">
                            @csrf

                            <div class="form-group row">
                                <label for="name" class="col-md-4 col-form-label text-md-right">Имя</label>

                                <div class="col-md-6">
                                    <input id="name" type="text"
                                           class="form-control @error('name') is-invalid @enderror" name="name"
                                           value="{{ old('name') }}" required autocomplete="name" autofocus>

                                    @error('name')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="shortName" class="col-md-4 col-form-label text-md-right">Короткое
                                    обозначение</label>

                                <div class="col-md-6">
                                    <input id="shortName" type="text"
                                           class="form-control @error('shortName') is-invalid @enderror"
                                           name="shortName" value="{{ old('shortName') }}"
                                           autocomplete="shortName">

                                    @error('shortName')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="isoName" class="col-md-4 col-form-label text-md-right">Обозначение
                                    ISO</label>

                                <div class="col-md-6">
                                    <input id="isoName" type="text"
                                           class="form-control @error('isoName') is-invalid @enderror"
                                           name="isoName" value="{{ old('isoName') }}"
                                           autocomplete="isoName">

                                    @error('isoName')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="country_id" class="col-md-4 col-form-label text-md-right">Страна</label>

                                <div class="col-md-6">
                                    <select id="country_id"
                                            class="form-control @error('country_id') is-invalid @enderror"
                                            name="country_id">
                                        @foreach($countries as $country)
                                            <option value="{{$country->id}}">{{$country->name}}</option>
                                        @endforeach
                                    </select>
                                    @error('country_id')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="form-group row mb-0">
                                <div class="col-md-6 offset-md-4">
                                    <button type="submit" class="btn btn-primary">
                                        {{ __('Добавить') }}
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
