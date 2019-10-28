@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card shadow">
                    <div class="card-header">Добавить тип товара</div>
                    <div class="card-body">
                        <form id="addItem" method="POST" action="{{route('items.store')}}">
                            @csrf

                            <div class="form-group row">
                                <label for="name" class="col-md-4 col-form-label text-md-right">Название</label>
                                <div class="col-md-6">
                                    <input id="name" placeholder="введите название" type="text"
                                           class="form-control @error('name') is-invalid @enderror"
                                           name="name" value="{{ old('name') }}" required
                                           autocomplete="name" autofocus>

                                    @error('name')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="unit" class="col-md-4 col-form-label text-md-right">Единица
                                    измерения</label>
                                <div class="col-md-6">
                                    <input id="unit" placeholder="например, шт, ед, пачка" type="text"
                                           class="form-control @error('unit') is-invalid @enderror"
                                           name="unit" value="{{ old('unit') }}" required
                                           autocomplete="unit" autofocus>

                                    @error('unit')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="tariffId" class="col-md-4 col-form-label text-md-right">Тариф</label>
                                <div class="col-md-6">
                                    <select id="tariffId" name="tariffId"
                                            class="form-control @error('tariffId') is-invalid @enderror">
                                        <option value="null" disabled>--Выберите тариф--</option>

                                        @foreach($tariffs as $tariff)
                                            <option value="{{$tariff->id}}"
                                                    @if($tariff->id === old('tariffId')) selected @endif>
                                                {{$tariff->name}}
                                            </option>
                                        @endforeach

                                    </select>

                                    @error('tariffId')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="form-group row justify-content-center">
                                <div class="input-group offset-md-2 col-md-6">
                                    <div id="onlyCustomPrice" class="input-group-prepend">
                                        <div class="input-group-text">
                                            <input type="hidden" name="onlyCustomPrice" value="0">
                                            <input type="checkbox" name="onlyCustomPrice"
                                                   @if(old('onlyCustomPrice')) checked @endif
                                                   value="1"
                                                   aria-label="Radio button for following text input">
                                        </div>
                                    </div>
                                    <input type="text" class="form-control @error('onlyCustomPrice') is-invalid @enderror"
                                           aria-label="Text input with radio button"
                                           value="Ручная цена" disabled>

                                    <b-tooltip target="onlyCustomPrice" triggers="hover">
                                        Данный пукт обладает наивысшим приоритетом при формировании цены
                                    </b-tooltip>

                                    @error('onlyCustomPrice')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="form-group row justify-content-center">
                                <div class="input-group offset-md-2 col-md-6">
                                    <div class="input-group-prepend">
                                        <div class="input-group-text">
                                            <input type="hidden" name="onlyAgreedPrice" value="0">
                                            <input type="checkbox" name="onlyAgreedPrice"
                                                   @if(old('onlyAgreedPrice'))checked @endif
                                                   value="1"
                                                   aria-label="Radio button for following text input">
                                        </div>
                                    </div>
                                    <input type="text"
                                           class="form-control @error('onlyAgreedPrice') is-invalid @enderror"
                                           aria-label="Text input with radio button"
                                           value="Всегда использовать договорную цену" disabled>

                                    @error('onlyAgreedPrice')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="form-group row justify-content-center">
                                <div class="input-group offset-md-2 col-md-6">
                                    <div class="input-group-prepend">
                                        <div class="input-group-text">
                                            <input type="hidden" name="applyDiscount" value="0">
                                            <input type="checkbox" name="applyDiscount"
                                                   @if(old('applyDiscount'))checked @endif
                                                   value="1"
                                                   aria-label="Radio button for following text input">
                                        </div>
                                    </div>
                                    <input type="text" class="form-control @error('applyDiscount') is-invalid @enderror"
                                           aria-label="Text input with radio button"
                                           value="Применять скидку" disabled>

                                    @error('applyDiscount')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
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
