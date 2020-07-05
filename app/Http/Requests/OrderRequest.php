<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Waavi\Sanitizer\Laravel\SanitizesInput;

class OrderRequest extends FormRequest
{
//    use SanitizesInput;

    /**
     *  Validation rules to be applied to the input.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'id' => 'nullable|exists:orders,id',
            'storedItemInfos' => 'required_if:orderId,!==,null',
            'branch_id' => 'required|exists:branches,id',
            'clientCode' => 'nullable|string',
            'clientName' => 'nullable|string',
            'clientPhone' => 'nullable|string',
            'clientEmail' => 'nullable|string',
            'storedItemInfos.*.width' => 'required|numeric',
            'storedItemInfos.*.height' => 'required|numeric',
            'storedItemInfos.*.length' => 'required|numeric',
            'storedItemInfos.*.weight' => 'required|numeric',
            'storedItemInfos.*.count' => 'required|numeric',
            'storedItemInfos.*.customPrice' => 'nullable|numeric',
            'storedItemInfos.*.item_id' => 'required|exists:items,id',
            'storedItemInfos.*.tariff_id' => 'required|exists:tariffs,id',
            'storedItemInfos.*.branch_id' => 'required|exists:branches,id',
            'storedItemInfos.*.customs_code_id' => 'required|exists:customs_codes,id',
            'customPrices' => 'required|array',
            'customPrices.*' => 'nullable|numeric'
        ];
    }
}
