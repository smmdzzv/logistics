<?php

namespace App\Http\Requests;

use App\Models\Users\Driver;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class TripRequest extends FormRequest
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
            'returnDate' => 'required|date',
            'departureDate' => 'required|date',
            'carId' => 'required|exists:cars,id',
            'driverId' => 'required',
            'code' => [
                'required',
                'string',
                'max:20',
                Rule::unique('trips')->ignore(request()->get('id'))
            ]
        ];
    }

    /**
     * Configure the validator instance.
     *
     * @param  \Illuminate\Validation\Validator  $validator
     * @return void
     */
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $driverId = request()->get('driverId');
            $driver = Driver::find($driverId);
            if(!isset($driver))
                $validator->errors()->add('driverId', 'Указанный пользователь не является водителем');
        });
    }
}
