<?php

namespace App\Http\Requests;

use App\Models\Car;
use App\Models\Trip;
use App\Models\Users\Driver;
use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Input;
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
            'hasTrailer' => 'required|boolean',
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
     * @param \Illuminate\Validation\Validator $validator
     * @return void
     */
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $trip = Trip::find(request()->get('id'));
            if($trip && !$trip->isEditable())
                $validator->errors()->add('departureDate', 'Невозможно редактировать рейс. Дата редактирования должна быть меньше даты отправления');

            $driverId = request()->get('driverId');
            $driver = Driver::find($driverId);
            if ($driver === null)
                $validator->errors()->add('driverId', 'Указанный пользователь не является водителем');

            $car = Car::with('fromChinaConsumption', 'toChinaConsumption')->find(request()->get('carId'));

            if (!$car->toChinaConsumption)
                return $validator->errors()->add('carId', 'Для указанной машины не утсановлен расход топлива в Китай');


            if (!$car->fromChinaConsumption)
                return $validator->errors()->add('carId', 'Для указанной машины не утсановлен расход топлива из Китая');

            Input::merge([
                'to_consumption_id' => $car->toChinaConsumption->id,
                'from_consumption_id' => $car->fromChinaConsumption->id,
            ]);
        });
    }
}
