<?php
/**
 * @author Sultonazar Mamadazizov <sultonazar.mamadazizov@mail.ru>
 */

namespace App\Http\Requests\Till;


use App\Models\Till\ExchangeRate;

class ExchangeValidationPolicy extends PaymentValidationPolicy
{
    public function apply()
    {
        $exchangeRate = ExchangeRate::where('id', $this->request->get('exchangeRate'))
            ->where('id', $this->request->get('exchangeRate'))
            ->where('from_currency_id', $this->request->get('secondPaidCurrency'))
            ->where('to_currency_id', $this->request->get('billCurrency'))
            ->first();

        if (!$exchangeRate)
            return $this->validator->errors()->add('exchangeRate',
                'Указанный курс валют не найден или не соответствует выбранным валютам. ');

        $this->validationPolicy->apply();
    }
}
