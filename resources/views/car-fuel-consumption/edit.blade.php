@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-10 col-lg-8">
                <div class="card">
                    <div class="card-header">Изменить расход топлива для {{$car->number}}</div>
                    <div class="card-body">
                        <form id="addCar" method="POST" action="{{route('car-fuel-consumption.update', $car)}}">
                            @csrf
                            @method('patch')
                            <input type="hidden" name="car_id" value="{{$car->id}}">

                            <input type="hidden" name="trailerNumber" value="{{$car->trailerNumber}}">
                            <div class="container">
                                <div class="row py-3">
                                    <h5 class="col-12 text-center">В Китай</h5>
                                </div>

                                <div class="form-group row">
                                    <label for="toChina_forEmpty" class="col-md-4 col-form-label text-md-right">Расход
                                        пустой машины</label>
                                    <div class="col-md-6">
                                        <input id="toChina_forEmpty"
                                               placeholder="в литрах на 100 км"
                                               type="number"
                                               step="0.001"
                                               class="form-control @error('toChina_forEmpty') is-invalid @enderror"
                                               name="toChina_forEmpty"
                                               value="{{ old('toChina_forEmpty') ?? $toChina->forEmpty}}" required
                                               autocomplete="toChina_forEmpty" autofocus>

                                        @error('toChina_forEmpty')
                                        <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                        @enderror
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label for="toChina_forLoaded" class="col-md-4 col-form-label text-md-right">Расход
                                        загруженной машины</label>
                                    <div class="col-md-6">
                                        <input id="toChina_forLoaded"
                                               placeholder="в литрах на 100 км"
                                               type="number"
                                               step="0.001"
                                               class="form-control @error('toChina_forLoaded') is-invalid @enderror"
                                               name="toChina_forLoaded"
                                               value="{{ old('toChina_forLoaded') ?? $toChina->forLoaded}}" required
                                               autocomplete="toChina_forLoaded" autofocus>

                                        @error('toChina_forLoaded')
                                        <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                        @enderror
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label for="toChina_forEmptyTrailer" class="col-md-4 col-form-label text-md-right">
                                        Расход пустой машины с прицепом
                                    </label>
                                    <div class="col-md-6">
                                        <input id="toChina_forEmptyTrailer"
                                               placeholder="в литрах на 100 км"
                                               type="number"
                                               step="0.001"
                                               class="form-control @error('toChina_forEmptyTrailer') is-invalid @enderror"
                                               name="toChina_forEmptyTrailer"
                                               value="{{ old('toChina_forEmptyTrailer') ?? $toChina->forEmptyTrailer}}"
                                               @if($car->trailerNumber) required @endif
                                               autocomplete="toChina_forEmptyTrailer" autofocus>

                                        <small class="form-text text-muted">при наличии прицепа обязательно к
                                            заполнению</small>

                                        @error('toChina_forEmptyTrailer')
                                        <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                        @enderror
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label for="toChina_forLoadedTrailer" class="col-md-4 col-form-label text-md-right">Расход
                                        загруженной машины с прицепом</label>
                                    <div class="col-md-6">
                                        <input id="toChina_forLoadedTrailer"
                                               placeholder="в литрах на 100 км"
                                               type="number"
                                               step="0.001"
                                               class="form-control @error('toChina_forLoadedTrailer') is-invalid @enderror"
                                               name="toChina_forLoadedTrailer"
                                               value="{{ old('toChina_forLoadedTrailer') ?? $toChina->forLoaded}}"
                                               @if($car->trailerNumber)required @endif
                                               autocomplete="toChina_forLoadedTrailer" autofocus>

                                        <small class="form-text text-muted">при наличии прицепа обязательно к
                                            заполнению</small>

                                        @error('toChina_forLoadedTrailer')
                                        <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <!--From China-->
                            <div class="container">
                                <div class="row py-3">
                                    <h5 class="col-12 text-center">Из Китая</h5>
                                </div>
                                <div class="form-group row">
                                    <label for="fromChina_forEmpty" class="col-md-4 col-form-label text-md-right">Расход
                                        пустой машины</label>
                                    <div class="col-md-6">
                                        <input id="fromChina_forEmpty"
                                               placeholder="в литрах на 100 км"
                                               type="number"
                                               step="0.001"
                                               class="form-control @error('fromChina_forEmpty') is-invalid @enderror"
                                               name="fromChina_forEmpty"
                                               value="{{ old('fromChina_forEmpty') ?? $fromChina->forEmpty}}" required
                                               autocomplete="fromChina_forEmpty" autofocus>

                                        @error('fromChina_forEmpty')
                                        <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                        @enderror
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label for="fromChina_forLoaded" class="col-md-4 col-form-label text-md-right">Расход
                                        загруженной машины</label>
                                    <div class="col-md-6">
                                        <input id="fromChina_forLoaded"
                                               placeholder="в литрах на 100 км"
                                               type="number"
                                               step="0.001"
                                               class="form-control @error('fromChina_forLoaded') is-invalid @enderror"
                                               name="fromChina_forLoaded"
                                               value="{{ old('fromChina_forLoaded') ?? $fromChina->forLoaded}}" required
                                               autocomplete="fromChina_forLoaded" autofocus>

                                        @error('fromChina_forLoaded')
                                        <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                        @enderror
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label for="fromChina_forEmptyTrailer"
                                           class="col-md-4 col-form-label text-md-right">
                                        Расход пустой машины с прицепом
                                    </label>
                                    <div class="col-md-6">
                                        <input id="fromChina_forEmptyTrailer"
                                               placeholder="в литрах на 100 км"
                                               type="number"
                                               step="0.001"
                                               class="form-control @error('fromChina_forEmptyTrailer') is-invalid @enderror"
                                               name="fromChina_forEmptyTrailer"
                                               value="{{ old('fromChina_forEmptyTrailer') ?? $fromChina->forEmptyTrailer}}"
                                               @if($car->trailerNumber)required @endif
                                               autocomplete="fromChina_forEmptyTrailer" autofocus>

                                        <small class="form-text text-muted">при наличии прицепа обязательно к
                                            заполнению</small>

                                        @error('fromChina_forEmptyTrailer')
                                        <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                        @enderror
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label for="fromChina_forLoadedTrailer"
                                           class="col-md-4 col-form-label text-md-right">Расход
                                        загруженной машины с прицепом</label>
                                    <div class="col-md-6">
                                        <input id="fromChina_forLoadedTrailer"
                                               placeholder="в литрах на 100 км"
                                               type="number"
                                               step="0.001"
                                               class="form-control @error('fromChina_forLoadedTrailer') is-invalid @enderror"
                                               name="fromChina_forLoadedTrailer"
                                               value="{{ old('fromChina_forLoadedTrailer') ?? $fromChina->forLoadedTrailer}}"
                                               @if($car->trailerNumber)required @endif
                                               autocomplete="fromChina_forLoadedTrailer" autofocus>

                                        <small class="form-text text-muted">при наличии прицепа обязательно к
                                            заполнению</small>

                                        @error('fromChina_forLoadedTrailer')
                                        <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                        @enderror
                                    </div>
                                </div>
                            </div>


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
@endsection
