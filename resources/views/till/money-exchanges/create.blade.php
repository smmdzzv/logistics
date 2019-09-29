@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card shadow">
                    <div class="card-header">Добавить курс валюты</div>

                    <div class="card-body">

                        <form method="POST" action="{{ route('money-exchanges.store') }}">
                            @csrf

                            <div class="form-group row mt-3">
                                <div class="col-md-4 mb-3 ">
                                    <label for="from">Конвертируемая</label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">Из</span>
                                        </div>
                                        <select id="from"
                                                type="text"
                                                class="form-control custom-select @error('from') is-invalid @enderror"
                                                name="from"
                                                autocomplete="type" required>
                                            <option disabled>--Выберите валюту--</option>
                                            @foreach($currencies as $currency)
                                                <option value="{{$currency->id}}"
                                                        @if($currency->id === old('from')) selected @endif>
                                                    {{$currency->name}} {{$currency->isoName}}</option>
                                            @endforeach
                                        </select>
                                        @error('from')
                                        <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-4 mb-3 ">
                                    <label for="to">Целевая</label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">В</span>
                                        </div>
                                        <select id="to"
                                                type="text"
                                                class="form-control custom-select @error('to') is-invalid @enderror"
                                                name="to"
                                                autocomplete="type" required>
                                            <option disabled>--Выберите валюту--</option>
                                            @foreach($currencies as $currency)
                                                <option value="{{$currency->id}}"
                                                        @if($currency->id === old('to')) selected @endif>
                                                    {{$currency->name}} {{$currency->isoName}}</option>
                                            @endforeach
                                        </select>
                                        @error('to')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-4 mb-3">
                                    <label for="coefficient">Курс</label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">&times;</span>
                                        </div>
                                        <input id="coefficient" type="text" placeholder="на единицу конвертируемой"
                                               class="form-control @error('coefficient') is-invalid @enderror" name="coefficient"
                                               value="{{ old('coefficient') }}" required autocomplete="coefficient" autofocus>
                                        <small class="form-text text-muted">Курс является коэффициентом на который домножается конвертируемая валюта.</small>
                                        @error('coefficient')
                                        <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="form-group row mb-0">
                                <div class="col-md-12 text-md-center">
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
@endsection
