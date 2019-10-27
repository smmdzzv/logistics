<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CarRequest extends FormRequest
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
            'id' => 'string',
            'number' => 'required|string|max:20',
            'length' => 'numeric',
            'height' => 'numeric',
            'width' => 'numeric',
            'maxCubage' => 'required|numeric',
            'maxWeight' => 'required|numeric',
            'serial' => 'nullable|string|max:40',
            'trailerNumber' => 'nullable|string|max:20',
            'trailerMaxCubage' => 'required_with:trailerNumber|nullable|numeric',
            'trailerMaxWeight' => 'required_with:trailerNumber|nullable|numeric',
        ];
    }
}
