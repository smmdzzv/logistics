<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CarFuelConsumptionsRequest extends FormRequest
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
            'toChina_forEmpty' => 'required|numeric',
            'toChina_forLoaded' => 'required|numeric',
            'toChina_forEmptyTrailer' => 'required_with:trailerNumber|nullable|numeric',
            'toChina_forLoadedTrailer' => 'required_with:trailerNumber|nullable|numeric',
            'fromChina_forEmpty' => 'required|numeric',
            'fromChina_forLoaded' => 'required|numeric',
            'fromChina_forEmptyTrailer' => 'required_with:trailerNumber|nullable|numeric',
            'fromChina_forLoadedTrailer' => 'required_with:trailerNumber|nullable|numeric',
        ];
    }
}
