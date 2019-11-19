@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">Добавить таможеный код</div>

                    <div class="card-body">

                        <form method="POST" action="{{ route('customs-code.store') }}">
                            @csrf

                            <div class="form-group row">
                                <label for="name" class="col-md-4 col-form-label text-md-right">Название</label>

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
                                    название</label>

                                <div class="col-md-6">
                                    <input id="shortName" type="text"
                                           class="form-control @error('shortName') is-invalid @enderror"
                                           name="shortName" value="{{ old('shortName') }}"
                                           autocomplete="shortName" required>

                                    @error('shortName')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="internationalName" class="col-md-4 col-form-label text-md-right">Международное
                                    название</label>

                                <div class="col-md-6">
                                    <input id="internationalName" type="text"
                                           class="form-control @error('internationalName') is-invalid @enderror"
                                           name="internationalName" value="{{ old('internationalName') }}"
                                           autocomplete="internationalName">

                                    @error('internationalName')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>
                            </div>

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
                                    <input id="price" type="number" step="0.01"
                                           class="form-control @error('price') is-invalid @enderror"
                                           name="price" value="{{ old('price') }}"
                                           autocomplete="price" required>

                                    @error('price')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="rate" class="col-md-4 col-form-label text-md-right">Ставка</label>

                                <div class="col-md-6">
                                    <input id="rate" type="number" step="0.01"
                                           class="form-control @error('rate') is-invalid @enderror"
                                           name="rate" value="{{ old('rate') }}"
                                           autocomplete="rate" required>

                                    @error('rate')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="vat" class="col-md-4 col-form-label text-md-right">НДС</label>

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
                                <label for="percentage" class="col-md-4 col-form-label text-md-right">Процент</label>

                                <div class="col-md-6">
                                    <input id="percentage" type="number" step="0.01"
                                           class="form-control @error('percentage') is-invalid @enderror"
                                           name="percentage" value="{{ old('percentage') }}"
                                           autocomplete="percentage" required>

                                    @error('percentage')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="calculateByPiece" class="col-md-4 col-form-label text-md-right">Рассчет
                                    поштучно</label>
                                <input type="hidden" name="calculateByPiece" value="0">
                                <div class="col-md-6">
                                    <input id="calculateByPiece" type="checkbox"
                                           class="form-control @error('calculateByPiece') is-invalid @enderror"
                                           name="calculateByPiece"
                                           @if(old('calculateByPiece') && old('calculateByPiece') === 'on')
                                           checked
                                        @endif
                                    >

                                    @error('calculateByPiece')
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
