<?php
/**
 *
 * @author Sultonazar Mamadazizov <sultonazar.mamadazizov@mail.ru>
 */

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TariffPriceHistoryRequest extends FormRequest
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
            'id' => 'nullable|string',
            'lowerLimit' => 'required|numeric',
            'mediumLimit' => 'required|numeric',
            'upperLimit' => 'required|numeric',
            'pricePerCube' => 'required|numeric',
            'discountForLowerLimit' => 'required|numeric',
            'discountForMediumLimit' => 'required|numeric',
            'pricePerExtraKg' => 'required|numeric',
            'agreedPricePerKg' => 'required|numeric',
            'maxWeightPerCube' => 'required|numeric',
            'maxCubage' => 'required|numeric',
            'tariff_id' => 'required|exists:tariffs,id',
            'created_at' => 'required|date',
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $this->request->replace([
                'id' => $this->request->get('id'),
                'tariff_id' => $this->request->get('tariff_id'),
                'created_at' =>$this->request->get('created_at'),
                'lowerLimit' => floatval($this->request->get('lowerLimit')),
                'mediumLimit' => floatval($this->request->get('mediumLimit')),
                'upperLimit' => floatval($this->request->get('upperLimit')),
                'pricePerCube' => floatval($this->request->get('pricePerCube')),
                'discountForLowerLimit' => floatval($this->request->get('discountForLowerLimit')),
                'discountForMediumLimit' => floatval($this->request->get('discountForMediumLimit')),
                'pricePerExtraKg' => floatval($this->request->get('pricePerExtraKg')),
                'agreedPricePerKg' => floatval($this->request->get('agreedPricePerKg')),
                'maxWeightPerCube' => floatval($this->request->get('maxWeightPerCube')),
                'maxCubage' => floatval($this->request->get('maxCubage')),
            ]);
        });
    }
}
