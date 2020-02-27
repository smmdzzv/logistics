@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">Создать тарифный план</div>
                    <div class="card-body p-4">
                        <form method="POST" action="{{route('tariff-price-histories.update', $history)}}">
                            @csrf
                            @method('patch')
                            <input type="hidden" name="id" value="{{$history->id}}">

                            <div class="form-group row align-items-baseline">
                                <div class="col-sm-3">
                                    <label for="tariff" class="col-form-label">Тариф</label>
                                    <select id="tariff"
                                            class="form-control
                                            @error('tariff') is-invalid @enderror"
                                            name="tariff_id"
                                            required
                                            autocomplete="tariff"
                                            autofocus>
                                        @foreach($tariffs as $tariff)
                                            <option value="{{$tariff->id}}"
                                                    @if(old('tariff_id') && old('tariff_id') === $tariff->id)
                                                        selected
                                                    @elseif(!old('tariff_id') && $history->tariff->id === $tariff->id) selected @endif
                                            >{{$tariff->name}}</option>
                                        @endforeach
                                    </select>

                                    @error('tariff')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>

                                <div class="col-sm-3">
                                    <label for="lowerLimit" class="col-form-label">Нижний предел</label>
                                    <input id="lowerLimit" placeholder="в кг" type="number" step="0.01"
                                           class="form-control @error('lowerLimit') is-invalid @enderror"
                                           name="lowerLimit" value="{{ old('lowerLimit') ??  $history->lowerLimit}}" required
                                           autocomplete="lowerLimit" autofocus>

                                    @error('lowerLimit')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>

                                <div class="col-sm-3">
                                    <label for="mediumLimit" class="col-form-label">Средний предел</label>
                                    <input id="mediumLimit" placeholder="в кг" type="number" step="0.01"
                                           class="form-control @error('mediumLimit') is-invalid @enderror"
                                           name="mediumLimit" value="{{ old('mediumLimit') ?? $history->mediumLimit }}" required
                                           autocomplete="mediumLimit" autofocus>

                                    @error('mediumLimit')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>

                                <div class="col-sm-3">
                                    <label for="upperLimit" class="col-form-label">Верхний предел</label>
                                    <input id="upperLimit" placeholder="в кг" type="number" step="0.01"
                                           class="form-control @error('upperLimit') is-invalid @enderror"
                                           name="upperLimit" value="{{ old('upperLimit') ?? $history->upperLimit }}" required
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
                                    <input id="pricePerCube" placeholder="в долларах" type="number" step="0.01"
                                           class="form-control @error('pricePerCube') is-invalid @enderror"
                                           name="pricePerCube" value="{{ old('pricePerCube') ?? $history->pricePerCube  }}" required
                                           autocomplete="pricePerCube" autofocus>

                                    @error('pricePerCube')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>

                                <div class="col-sm-3">
                                    <label for="discountForLowerLimit" class="col-form-label">Скидка НП</label>
                                    <input id="discountForLowerLimit" placeholder="нижний предел" type="number" step="0.01"
                                           class="form-control @error('discountForLowerLimit') is-invalid @enderror"
                                           name="discountForLowerLimit" value="{{ old('discountForLowerLimit') ?? $history->discountForLowerLimit  }}"
                                           required autocomplete="discountForLowerLimit" autofocus>

                                    @error('discountForLowerLimit')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>

                                <div class="col-sm-3">
                                    <label for="discountForMediumLimit" class="col-form-label">Скидка СП</label>
                                    <input id="discountForMediumLimit" placeholder="средний предел" type="number" step="0.01"
                                           class="form-control @error('discountForMediumLimit') is-invalid @enderror"
                                           name="discountForMediumLimit" value="{{ old('discountForMediumLimit')  ?? $history->discountForMediumLimit }}"
                                           required autocomplete="discountForMediumLimit" autofocus>

                                    @error('discountForMediumLimit')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>

                                <div class="col-sm-3">
                                    <label for="maxWeightPerCube" class="col-form-label">Макс. вес куба</label>
                                    <input id="maxWeightPerCube" placeholder="норма в кг" type="number" step="0.01"
                                           class="form-control @error('maxWeightPerCube') is-invalid @enderror"
                                           name="maxWeightPerCube" value="{{ old('maxWeightPerCube')  ?? $history->maxWeightPerCube }}" required
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
                                    <label for="agreedPricePerKg" class="col-form-label">Цена за кг (дог-ная)</label>
                                    <input id="agreedPricePerKg"
                                           placeholder="договорная"
                                           type="number" step="0.01"
                                           class="form-control @error('agreedPricePerKg') is-invalid @enderror"
                                           name="agreedPricePerKg"
                                           value="{{ old('agreedPricePerKg')  ?? $history->agreedPricePerKg }}"
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
                                    <label for="pricePerExtraKg" class="col-form-label">Цена за кг (сверхнор-ая)</label>
                                    <input id="pricePerExtraKg" placeholder="сверх нормы" type="number" step="0.01"
                                           class="form-control @error('pricePerExtraKg') is-invalid @enderror"
                                           name="pricePerExtraKg" value="{{ old('pricePerExtraKg') ?? $history->pricePerExtraKg  }}" required
                                           autocomplete="pricePerExtraKg" autofocus>

                                    @error('pricePerExtraKg')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>

                                <div class="col-sm-3">
                                    <label for="maxCubage" class="col-form-label">Макс. кубатура</label>
                                    <input id="maxCubage" placeholder="рейса" type="number" step="0.01"
                                           class="form-control @error('maxCubage') is-invalid @enderror"
                                           name="maxCubage" value="{{ old('maxCubage') ?? $history->maxCubage  }}" required
                                           autocomplete="maxCubage" autofocus>

                                    @error('maxCubage')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>


                                <div class="col-sm-3">
                                    <label for="maxWeight" class="col-form-label">Макс. вес</label>
                                    <input id="maxWeight" placeholder="рейса" type="number" step="0.01"
                                           class="form-control @error('maxWeight') is-invalid @enderror"
                                           name="maxWeight" value="{{ old('maxWeight')  ?? $history->maxWeight }}" required
                                           autocomplete="maxWeight" autofocus>

                                    @error('maxWeight')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="form-group row">
                                <div class="col-sm-3">
                                    <label for="totalMoney" class="col-form-label">Сумма</label>
                                    <input id="totalMoney"
                                           placeholder="в долларах"
                                           type="number" step="0.01"
                                           class="form-control
                                            @error('totalMoney') is-invalid @enderror"
                                           name="totalMoney"
                                           value="{{ old('totalMoney')  ?? $history->totalMoney }}"
                                           required
                                           autocomplete="totalMoney"
                                           autofocus>

                                    @error('totalMoney')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>

                                <div class="col-sm-5 col-md-3">
                                    <label for="created_at" class="col-form-label">Дата начала</label>
                                    <input id="created_at"
                                           placeholder="в долларах"
                                           type="date"
                                           class="form-control
                                            @error('created_at') is-invalid @enderror"
                                           name="created_at"
                                           value="{{ old('created_at')  ??  \Carbon\Carbon::parse($history->created_at)->toDateString()}}"
                                           required
                                           autocomplete="created_at"
                                           autofocus>

                                    <b-tooltip target="created_at" triggers="hover">
                                        Обновленные расценки будут применяться с указанной даты
                                    </b-tooltip>

                                    @error('created_at')
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
    document.addEventListener("DOMContentLoaded", function () {
        $('#maxWeight').change(updateData);
        $('#maxCubage').change(updateData);
        $('#agreedPricePerKg').change(updatePricePerExtraKg)
    });

    function updatePricePerExtraKg() {
        let maxWeightPerCube = parseFloat($('#maxWeightPerCube').val());
        let upperLimit = parseFloat($('#upperLimit').val());
        let pricePerCube = parseFloat($('#pricePerCube').val());
        let agreedPricePerKg = parseFloat($('#agreedPricePerKg').val());

        let diff = maxWeightPerCube - upperLimit;
        if (diff !== 0) {
            let result = (agreedPricePerKg * maxWeightPerCube - pricePerCube) / diff;
            result = result > 0 ? result : 0;
            $('#pricePerExtraKg').val(result.toFixed(2));
        }
    }

    function updateData() {
        let pricePerCube = parseFloat($('#pricePerCube').val());
        let maxCubage = parseFloat($('#maxCubage').val());
        let maxWeight = parseFloat($('#maxWeight').val());
        let upperLimit = parseFloat($('#upperLimit').val());
        let pricePerExtraKg = parseFloat($('#pricePerExtraKg').val());

        let price = pricePerCube + (maxWeight / maxCubage - upperLimit) * pricePerExtraKg;
        let totalMoney = price * maxCubage;
        $('#totalMoney').val(totalMoney.toFixed(2));

        let agreedPricePerKg = totalMoney / maxWeight;
        $('#agreedPricePerKg').val(agreedPricePerKg.toFixed(2));

        let maxWeightPerCube = calculateMaxWeightPerCube(pricePerCube, upperLimit, pricePerExtraKg, agreedPricePerKg);
        $('#maxWeightPerCube').val(maxWeightPerCube.toFixed(2));
    }

    //TODO analyze this
    function calculateMaxWeightPerCube(pricePerCube, upperLimit, pricePerExtraKg, agreedPricePerKg, step = 0.1) {
        let m = upperLimit + step;
        let s = pricePerCube + (m - upperLimit) * pricePerExtraKg;

        if (m !== 0 && agreedPricePerKg !== 0 && pricePerExtraKg < agreedPricePerKg && agreedPricePerKg < s / m) {
            s = pricePerCube + (m + upperLimit) + pricePerExtraKg;
            let sn = s / m;
            let i = 0;
            while (agreedPricePerKg < sn && i < 10000) // sn - agreedPricePerKg > 0.001
            {
                m = m + step;
                s = pricePerCube + (m - upperLimit) * pricePerExtraKg;
                i = i + 1;
                sn = s / m;
            }
        }

        return m;
    }
</script>

