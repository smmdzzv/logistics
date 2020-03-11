<?php

namespace App\Http\Requests;

use App\Models\Branch;
use App\Models\Car;
use App\Models\Trip;
use App\Models\Users\Driver;
use Illuminate\Database\Eloquent\Builder;
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
            'destination_branch_id' => 'required|string',
            'departure_branch_id' => 'required|string',
            'departureDate' => 'required|date',
            'carId' => 'required|exists:cars,id',
            'hasTrailer' => 'required|boolean',
//            'emptyToDestination' => 'required|boolean',
//            'emptyFromDestination' => 'required|boolean',
            'routeLengthToDestination' => 'required|integer|min:0',
            'routeLengthWithCargoTo' => 'required|integer|min:0',
            'routeLengthFromDestination' => 'required|integer|min:0',
            'cargoWeightTo' => 'required|integer|min:0',
            'trailerCargoWeightTo' => 'required|integer|min:0',
            'cargoWeightFrom' => 'required|integer|min:0',
            'trailerCargoWeightFrom' => 'required|integer|min:0',
            'mileageBefore' => 'required|integer|min:0',
            'driverId' => 'required',
            'code' => [
                'required',
                'string',
                'max:20',
                Rule::unique('trips')->ignore(request()->get('id'))
            ],
            'mileageAfter' => 'nullable|number',
            'contractPrice' => 'nullable|number',
            'driverSalary' => 'nullable|number',
            'tripCoast' => 'nullable|number',
            'otherExpanses' => 'nullable|number',
            'fine' => 'nullable|number'
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(
            function ($validator) {
                $trip = Trip::find(request()->get('id'));
                if ($trip && !$trip->isEditable())
                    $validator->errors()->add('departureDate', 'Невозможно редактировать рейс. Дата редактирования должна быть меньше даты отправления');

                $driverId = request()->get('driverId');
                $driver = Driver::find($driverId);
                if ($driver === null)
                    $validator->errors()->add('driverId', 'Указанный пользователь не является водителем');

                $car = Car::find(request()->get('carId'));

                $destinationCountryId = Branch::find(request()->get('destination_branch_id'))->country;
                $departureCountryId = Branch::find(request()->get('departure_branch_id'))->country;

                $destinationConsumption = $car->fuelConsumption()
                    ->latest()
                    ->whereHas('destination', function (Builder $query) use ($destinationCountryId) {
                        $query->where('id', '=', $destinationCountryId);
                    })->first();

                if (!$destinationConsumption)
                    return $validator->errors()->add('carId', 'Для указанной машины не утсановлен расход топлива в страну назначения');

                $departureConsumption = $car->fuelConsumption()
                    ->latest()
                    ->whereHas('destination', function (Builder $query) use ($departureCountryId) {
                        $query->where('id', '=', $departureCountryId);
                    })->first();

                if (!$departureConsumption)
                    return $validator->errors()->add('carId', 'Для указанной машины не утсановлен расход топлива из страны назначения');

                Input::merge([
                    'to_consumption_id' => $destinationConsumption->id,
                    'from_consumption_id' => $departureConsumption->id,
                ]);

                if (!$this->request->get('mileageAfter'))
                    Input::merge([
                        'mileageAfter' => $this->request->get('routeLengthFromDestination')
                    ]);

            });
    }
}
