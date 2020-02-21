@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-10">
                <div class="card">
                    <div class="card-header">Добавить таможеный код</div>

                    <div class="card-body">

                        <form method="POST" action="{{ route('customs-code.store') }}">
                            @csrf
                            @method('post')

                            <div class="form-group row">
                                <label for="name" class="col-md-4 col-form-label text-md-right">Наименование</label>

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

{{--                            <div class="form-group row">--}}
{{--                                <label for="shortName" class="col-md-4 col-form-label text-md-right">Короткое--}}
{{--                                    название</label>--}}

{{--                                <div class="col-md-6">--}}
{{--                                    <input id="shortName" type="text"--}}
{{--                                           class="form-control @error('shortName') is-invalid @enderror"--}}
{{--                                           name="shortName" value="{{ old('shortName') }}"--}}
{{--                                           autocomplete="shortName" required>--}}

{{--                                    @error('shortName')--}}
{{--                                    <span class="invalid-feedback" role="alert">--}}
{{--                                        <strong>{{ $message }}</strong>--}}
{{--                                    </span>--}}
{{--                                    @enderror--}}
{{--                                </div>--}}
{{--                            </div>--}}

{{--                            <div class="form-group row">--}}
{{--                                <label for="internationalName" class="col-md-4 col-form-label text-md-right">Международное--}}
{{--                                    название</label>--}}

{{--                                <div class="col-md-6">--}}
{{--                                    <input id="internationalName" type="text"--}}
{{--                                           class="form-control @error('internationalName') is-invalid @enderror"--}}
{{--                                           name="internationalName" value="{{ old('internationalName') }}"--}}
{{--                                           autocomplete="internationalName">--}}

{{--                                    @error('internationalName')--}}
{{--                                    <span class="invalid-feedback" role="alert">--}}
{{--                                        <strong>{{ $message }}</strong>--}}
{{--                                    </span>--}}
{{--                                    @enderror--}}
{{--                                </div>--}}
{{--                            </div>--}}

                            <div class="form-group row">
                                <label for="code" class="col-md-4 col-form-label text-md-right">Код</label>

                                <div class="col-md-6">
                                    <input id="code" type="text"
                                           class="form-control @error('code') is-invalid @enderror"
                                           name="code" value="{{ old('code') }}"
                                           autocomplete="code" required>

                                    @error('code')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="price" class="col-md-4 col-form-label text-md-right">Цена</label>

                                <div class="col-md-6">
                                    <b-tooltip target="price" triggers="hover">
                                        Цена определяется таможенной службой
                                    </b-tooltip>
                                    <input id="price" type="number" step="0.01"
                                           class="form-control @error('price') is-invalid @enderror"
                                           name="price" value="{{ old('price') }}"
                                           placeholder="за тонну или штуку товара"
                                           autocomplete="price" required>

                                    @error('price')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="interestRate" class="col-md-4 col-form-label text-md-right">Базовая ставка, %</label>

                                <div class="col-md-6">
                                    <input id="interestRate" type="number" step="0.01"
                                           class="form-control @error('interestRate') is-invalid @enderror"
                                           name="interestRate" value="{{ old('interestRate') }}"
                                           autocomplete="interestRate" required>

                                    @error('interestRate')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="vat" class="col-md-4 col-form-label text-md-right">НДС, %</label>

                                <div class="col-md-6">
                                    <input id="vat" type="number" step="0.01"
                                           class="form-control @error('vat') is-invalid @enderror"
                                           name="vat" value="{{ old('vat') }}"
                                           autocomplete="vat" required>

                                    @error('vat')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="totalRate" class="col-md-4 col-form-label text-md-right">Итоговая пошлина, %</label>

                                <div class="col-md-6">
                                    <input id="totalRate" type="number" step="0.01"
                                           class="form-control @error('totalRate') is-invalid @enderror"
                                           name="totalRate" value="{{ old('totalRate') }}"
                                           autocomplete="totalRate" required>

                                    @error('totalRate')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="isCalculatedByPiece" class="col-md-4 col-form-label text-md-right">Поштучный рассчет</label>
                                <input type="hidden" name="isCalculatedByPiece" value="0">
                                <div class="col-md-6">
                                    <input type="hidden" name="isCalculatedByPiece" value="0">
                                    <input id="isCalculatedByPiece" type="checkbox"
                                           class="form-control @error('isCalculatedByPiece') is-invalid @enderror"
                                           name="isCalculatedByPiece"
                                           value="1"
                                           @if(old('isCalculatedByPiece') && old('isCalculatedByPiece') === 'on')
                                           checked
                                        @endif
                                    >

                                    @error('isCalculatedByPiece')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>
                            </div>

                            @if ($errors->any())
                                <div class="alert alert-danger">
                                    <ul>
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif
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
