<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TariffPriceHistoryRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'lowerLimit' => 'required|numeric',
            'mediumLimit' => 'required|numeric',
            'upperLimit' => 'required|numeric',
            'pricePerCube' => 'required|numeric',
            'discountForLowerLimit' => 'required|numeric',
            'discountForMediumLimit' => 'required|numeric',
            'pricePerExtraKg' => 'required|numeric',
            'agreedPricePerKg' => 'required|numeric',
            'maxWeightPerCube' => 'required|numeric',
            'maxCubage' => 'required|numeric',
            'tariff_id' => 'required|exists:tariffs,id',
            'created_at' => 'required|date'
        ];
    }
}
