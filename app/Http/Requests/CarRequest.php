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
            'trailerNumber' => 'string|max:20',
            'length' => 'numeric',
            'height' => 'numeric',
            'width' => 'numeric',
            'maxCubage' => 'required|numeric',
            'maxWeight' => 'required|numeric',
            'serial' => 'string|max:40'
        ];
    }
}