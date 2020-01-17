@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">Добавить потерянный заказ</div>
                    <div class="card-body">
                        <form method="post" action="{{route('lost-items.store')}}">
                            @csrf

                            <div class="form-group row">
                                <label for="storageItemCode" class="col-md-4 col-form-label text-md-right">Код
                                    товара</label>

                                <div class="col-md-6">
                                    <input id="storedItemCode"
                                           type="text"
                                           class="form-control   @error('storedItemCode') is-invalid @enderror"
                                           name="storedItemCode"
                                           value="{{ old('storedItemCode') }}"
                                           autocomplete="storedItemCode"
                                           autofocus>

                                    @error('storedItemCode')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="discount" class="col-md-4 col-form-label text-md-right">Скидка</label>

                                <div class="col-md-6">
                                    <input id="discount"
                                           type="number"
                                           step="0.01"
                                           class="form-control @error('discount') is-invalid @enderror"
                                           placeholder="в долларах США"
                                           name="discount"
                                           value="{{ old('discount') }}"
                                           autocomplete="discount"
                                           autofocus>

                                    @error('discount')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="placeCount" class="col-md-4 col-form-label text-md-right">Кол-во товаров</label>

                                <div class="col-md-6">
                                    <input id="placeCount"
                                           type="number"
                                           class="form-control @error('placeCount') is-invalid @enderror"
                                           name="placeCount"
                                           value="{{ old('placeCount') }}"
                                           autocomplete="placeCount"
                                           autofocus>

                                    @error('placeCount')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="form-group row mb-0">
                                <div class="col-md-6 offset-md-4">
                                    <button type="submit" class="btn btn-primary">
                                       Сохранить
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
