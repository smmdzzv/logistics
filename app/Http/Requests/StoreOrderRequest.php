<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Waavi\Sanitizer\Laravel\SanitizesInput;

class StoreOrderRequest extends FormRequest
{
    use SanitizesInput;

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        //TODO employee
        return $this->user()->hasRole('employee');
    }

    /**
     *  Validation rules to be applied to the input.
     *
     *  @return array
     */
    public function rules()
    {
        $rules =['storedItems' =>'required|array'];
        foreach ($this->request->get('storedItems') as $key => $value){
            $rules['storedItems.'.$key.'.width'] = 'required|numeric';
            $rules['storedItems.'.$key.'.height'] = 'required|numeric';
            $rules['storedItems.'.$key.'.length'] = 'required|numeric';
            $rules['storedItems.'.$key.'.weight'] = 'required|numeric';
            $rules['storedItems.'.$key.'.count'] = 'required|numeric';
            $rules['storedItems.'.$key.'.totalCubage'] = 'required|numeric';
            $rules['storedItems.'.$key.'.totalWeight'] = 'required|numeric';
            $rules['storedItems.'.$key.'.price'] = 'required|numeric';

            $rules['storedItems.'.$key.'.branch'] = 'required|array';
            $rules['storedItems.'.$key.'.branch.id'] = 'required|exists:branches,id';

            $rules['storedItems.'.$key.'.tariffPricing'] = 'required|array';
            $rules['storedItems.'.$key.'.tariffPricing.id'] = 'required|exists:tariff_price_histories,id';


            $rules['storedItems.'.$key.'.item'] = 'required|array';
            $rules['storedItems.'.$key.'.item.id'] = 'required|exists:items,id';
        }
        return $rules;
    }

    /**
     *  Filters to be applied to the input.
     *
     *  @return void
     */
    public function filters()
    {
        return [];
    }
}
