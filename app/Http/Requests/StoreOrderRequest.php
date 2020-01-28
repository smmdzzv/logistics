<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Waavi\Sanitizer\Laravel\SanitizesInput;

class StoreOrderRequest extends FormRequest
{
    use SanitizesInput;

    /**
     *  Validation rules to be applied to the input.
     *
     * @return array
     */
    public function rules()
    {
        $rules = ['storedItemInfos' => 'required|array'];

        $rules['orderId'] = 'nullable|exists:orders,id';
        $rules['clientId'] = 'required|exists:users,id';

        $rules['storedItemInfos.*.width'] = 'required|numeric';
        $rules['storedItemInfos.*.height'] = 'required|numeric';
        $rules['storedItemInfos.*.length'] = 'required|numeric';
        $rules['storedItemInfos.*.weight'] = 'required|numeric';
        $rules['storedItemInfos.*.count'] = 'required|numeric';
        $rules['storedItemInfos.*.totalCubage'] = 'required|numeric';
        $rules['storedItemInfos.*.totalWeight'] = 'required|numeric';
        $rules['storedItemInfos.*.price'] = 'required|numeric';
//        $rules['storedItemInfos.*.placeCount'] = 'required|numeric';
        $rules['storedItemInfos.*.billingInfo.tariffPricing'] = 'required|array';
        $rules['storedItemInfos.*.billingInfo.tariffPricing.id'] = 'required|exists:tariff_price_histories,id';
        $rules['storedItemInfos.*.item'] = 'required|array';
        $rules['storedItemInfos.*.item.id'] = 'required|exists:items,id';
        $rules['storedItemInfos.*.customsCode.id'] = 'required|exists:customs_codes,id';

        return $rules;
    }

    /**
     *  Filters to be applied to the input.
     *
     * @return void
     */
    public function filters()
    {
        return [];
    }
}
