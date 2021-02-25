<?php
/**
 * @author Sultonazar Mamadazizov <sultonazar.mamadazizov@mail.ru>
 */

namespace App\Http\Requests\Till;


use App\Models\Till\ExchangeRate;

class PaymentAmountValidationPolicy extends PaymentValidationPolicy
{
    public function apply()
    {
        $exchangeRate = ExchangeRate::where('id', $this->request->get('exchangeRate'))
            ->where('id', $this->request->get('exchangeRate'))
            ->where('from_currency_id', $this->request->get('secondPaidCurrency'))
            ->where('to_currency_id', $this->request->get('billCurrency'))
            ->first();

        if ($this->request->get('secondPaidCurrency') && $this->request->get('paidAmountInSecondCurrency') > 0) {
            if (!$exchangeRate && !$this->request->get('customExchangeRate'))
                return $this->validator->errors()->add('secondPaidCurrency',
                    'Для указанной валюты Необходима конвертация. ');

            $rate = $exchangeRate->coefficient ?? $this->request->get('customExchangeRate');
            $amount = round($this->request->get('paidAmountInBillCurrency') +
                $this->request->get('paidAmountInSecondCurrency') * $rate, 2);

            if ($this->request->get('billAmount') - $amount != 0)
                return $this->validator->errors()->add('paidAmountInBillCurrency',
                    'Сумма оплаты не соотвествует требуемой с учетом конвертации. Требуемая '
                    . $this->request->get('billAmount') . '. Оплаченная ' . $amount);

        } else {
            if ($this->request->get('billAmount') !== $this->request->get('paidAmountInBillCurrency'))
                return $this->validator->errors()->add('paidAmountInBillCurrency',
                    'Сумма к оплате не равняется требуемой сумме. ');
        }

        $this->validationPolicy->apply();
    }
}
