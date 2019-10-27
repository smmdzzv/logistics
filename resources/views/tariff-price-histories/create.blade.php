@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">Создать тарифный план</div>
                    <div class="card-body p-4">
                        <form method="POST" action="{{route('tariff-price-histories.store')}}">
                            @csrf

                            <div class="form-group row">
                                <div class="col-sm-3">
                                    <label for="tariff" class="col-form-label">Тариф</label>
                                    <select id="tariff" placeholder="в кг"
                                            class="form-control @error('tariff') is-invalid @enderror"
                                            name="tariff_id" value="{{ old('tariff') }}" required
                                            @if($selectedTariff) disabled @endif
                                            autocomplete="tariff" autofocus>
                                        @if($selectedTariff)
                                            <option value="{{$selectedTariff->id}}">{{$selectedTariff->name}}</option>
                                        @else
                                            @foreach($tariffs as $tariff)
                                                <option value="{{$tariff->id}}">{{$tariff->name}}</option>
                                            @endforeach
                                        @endif
                                    </select>

                                    @error('tariff')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>

                                <div class="col-sm-3">
                                    <label for="lowerLimit" class="col-form-label">Нижний предел</label>
                                    <input id="lowerLimit" placeholder="в кг" type="text"
                                           class="form-control @error('lowerLimit') is-invalid @enderror"
                                           name="lowerLimit" value="{{ old('lowerLimit') }}" required
                                           autocomplete="lowerLimit" autofocus>

                                    @error('lowerLimit')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>

                                <div class="col-sm-3">
                                    <label for="mediumLimit" class="col-form-label">Средний предел</label>
                                    <input id="mediumLimit" placeholder="в кг" type="text"
                                           class="form-control @error('mediumLimit') is-invalid @enderror"
                                           name="mediumLimit" value="{{ old('mediumLimit') }}" required
                                           autocomplete="mediumLimit" autofocus>

                                    @error('mediumLimit')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>

                                <div class="col-sm-3">
                                    <label for="upperLimit" class="col-form-label">Верхний предел</label>
                                    <input id="upperLimit" placeholder="в кг" type="text"
                                           class="form-control @error('upperLimit') is-invalid @enderror"
                                           name="upperLimit" value="{{ old('upperLimit') }}" required
                                           autocomplete="upperLimit" autofocus>

                                    @error('upperLimit')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="form-group row align-items-baseline">
                                <div class="col-sm-3">
                                    <label for="pricePerCube" class="col-form-label">Цена за куб</label>
                                    <input id="pricePerCube" placeholder="в долларах" type="text"
                                           class="form-control @error('pricePerCube') is-invalid @enderror"
                                           name="pricePerCube" value="{{ old('pricePerCube') }}" required
                                           autocomplete="pricePerCube" autofocus>

                                    @error('pricePerCube')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>

                                <div class="col-sm-3">
                                    <label for="discountForLowerLimit" class="col-form-label">Скидка НП</label>
                                    <input id="discountForLowerLimit" placeholder="нижний предел" type="text"
                                           class="form-control @error('discountForLowerLimit') is-invalid @enderror"
                                           name="discountForLowerLimit" value="{{ old('discountForLowerLimit') }}"
                                           required autocomplete="discountForLowerLimit" autofocus>

                                    @error('discountForLowerLimit')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>

                                <div class="col-sm-3">
                                    <label for="discountForMediumLimit" class="col-form-label">Скидка СП</label>
                                    <input id="discountForMediumLimit" placeholder="средний предел" type="text"
                                           class="form-control @error('discountForMediumLimit') is-invalid @enderror"
                                           name="discountForMediumLimit" value="{{ old('discountForMediumLimit') }}"
                                           required autocomplete="discountForMediumLimit" autofocus>

                                    @error('discountForMediumLimit')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>

                                <div class="col-sm-3">
                                    <label for="maxWeightPerCube" class="col-form-label">Макс. вес куба</label>
                                    <input id="maxWeightPerCube" placeholder="норма в кг" type="text"
                                           class="form-control @error('maxWeightPerCube') is-invalid @enderror"
                                           name="maxWeightPerCube" value="{{ old('maxWeightPerCube') }}" required
                                           autocomplete="maxWeightPerCube" autofocus>

                                    @error('maxWeightPerCube')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>

                            </div>

                            <div class="form-group row align-items-baseline">
                                <div class="col-sm-3">
                                    <label for="agreedPricePerKg" class="col-form-label">Цена за кг</label>
                                    <input id="agreedPricePerKg"
                                           onchange="updatePricePerExtraKg(this)"
                                           placeholder="договорная"
                                           type="text"
                                           class="form-control @error('agreedPricePerKg') is-invalid @enderror"
                                           name="agreedPricePerKg"
                                           value="{{ old('agreedPricePerKg') }}"
                                           required
                                           autocomplete="agreedPricePerKg"
                                           autofocus>

                                    @error('agreedPricePerKg')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>

                                <div class="col-sm-3">
                                    <label for="pricePerExtraKg" class="col-form-label">Цена за кг</label>
                                    <input id="pricePerExtraKg" placeholder="сверх нормы" type="text"
                                           class="form-control @error('pricePerExtraKg') is-invalid @enderror"
                                           name="pricePerExtraKg" value="{{ old('pricePerExtraKg') }}" required
                                           autocomplete="pricePerExtraKg" autofocus>

                                    @error('pricePerExtraKg')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>

                                <div class="col-sm-3">
                                    <label for="maxCubage" class="col-form-label">Макс. кубатура</label>
                                    <input id="maxCubage" placeholder="рейса" type="text"
                                           class="form-control @error('maxCubage') is-invalid @enderror"
                                           name="maxCubage" value="{{ old('maxCubage') }}" required
                                           autocomplete="maxCubage" autofocus>

                                    @error('maxCubage')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>


                                <div class="col-sm-3">
                                    <label for="maxWeight" class="col-form-label">Макс. вес</label>
                                    <input id="maxWeight" placeholder="рейса" type="text"
                                           class="form-control @error('maxWeight') is-invalid @enderror"
                                           name="maxWeight" value="{{ old('maxWeight') }}" required
                                           autocomplete="maxWeight" autofocus>

                                    @error('maxWeight')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="form-group row mb-0">
                                <div class="col-12 text-center pt-3">
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

<script>
    function updatePricePerExtraKg(element) {
        let maxWeightPerCube = parseFloat($('#maxWeightPerCube').val());
        let upperLimit = parseFloat($('#upperLimit').val());
        let pricePerCube = parseFloat($('#pricePerCube').val());
        let agreedPrice = parseFloat($(element).val());

        let diff = maxWeightPerCube - upperLimit;
        if (diff !== 0) {
            let result = (agreedPrice * maxWeightPerCube - pricePerCube) / diff;
            result = result > 0 ? result : 0;
            $('#pricePerExtraKg').val(result.toFixed(2));
        }

    }
</script>

