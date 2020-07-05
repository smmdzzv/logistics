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
//            'storedItemInfos.*.tariff_price_history_id' => 'required|exists:tariff_price_histories,id',
            'storedItemInfos.*.tariff_id' => 'required|exists:tariffs,id',
            'storedItemInfos.*.branch_id' => 'required|exists:branches,id',
            'storedItemInfos.*.customs_code_tax_id' => 'required|exists:customs_code_taxes,id',
        ];


//        $rules['orderId'] = 'nullable|exists:orders,id';
//        $rules = ['storedItemInfos' => 'required_if:orderId,!==,null'];

        //Client validation
//        $rules['clientCode'] = 'nullable|string';
//        $rules['clientName'] = 'nullable|string';
//        $rules['clientPhone'] = 'nullable|string';
//        $rules['clientEmail'] = 'nullable|string';

        //stored item infos validation
//        $rules['storedItemInfos.*.width'] = 'required|numeric';
//        $rules['storedItemInfos.*.height'] = 'required|numeric';
//        $rules['storedItemInfos.*.length'] = 'required|numeric';
//        $rules['storedItemInfos.*.weight'] = 'required|numeric';
//        $rules['storedItemInfos.*.count'] = 'required|numeric';
//        $rules['storedItemInfos.*.customPrice'] = 'nullable|numeric';

        //stored item infos relations validation
//        $rules['storedItemInfos.*.item_id'] = 'required|exists:items,id';
//        $rules['storedItemInfos.*.tariff_price_history_id'] = 'required|exists:tariff_price_histories,id';
//        $rules['storedItemInfos.*.tariff_id'] = 'required|exists:tariffs,id';
//        $rules['storedItemInfos.*.branch_id'] = 'required|exists:branches,id';
//        $rules['storedItemInfos.*.customs_code_tax_id'] = 'required|exists:customs_code_taxes,id';

//        return $rules;
    }

    /**
     *  Filters to be applied to the input.
     *
     * @return void
     */
//    public function filters()
//    {
//        return [];
//    }
}
