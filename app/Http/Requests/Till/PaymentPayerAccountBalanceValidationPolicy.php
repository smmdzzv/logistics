<?php
/**
 * @author Sultonazar Mamadazizov <sultonazar.mamadazizov@mail.ru>
 */

namespace App\Http\Requests\Till;


class PaymentPayerAccountBalanceValidationPolicy extends PaymentValidationPolicy
{
    public function apply()
    {
        if ($this->request->get('payer_type') === 'branch') {

            $payerAccountBillCurrency = $this->payer->accounts()
                ->where('currency_id', $this->request->get('billCurrency'))
                ->first();

            $payerAccountSecondCurrency = $this->payer->accounts()
                ->where('currency_id', $this->request->get('secondPaidCurrency'))
                ->first();

            if (!$payerAccountBillCurrency)
                return $this->validator->errors()
                    ->add('billCurrency', 'Не найден счет плательщика в оплачиваемой валюте. ');

            if ($payerAccountBillCurrency->balance - $this->request->get('paidAmountInBillCurrency') < 0)
                return $this->validator->errors()->add('payer',
                        'Недостаточно средств на счету плательщика - '
                        . $payerAccountBillCurrency->balance . ' '
                        . $payerAccountBillCurrency->currency->isoName) . '.';

            if ($this->request->get('paidAmountInSecondCurrency') > 0) {
                if (!$payerAccountSecondCurrency)
                    return $this->validator->errors()
                        ->add('secondPaidCurrency', 'Не найден счет плательщика в оплачиваемой валюте. ');

                if ($payerAccountSecondCurrency->balance - $this->request->get('paidAmountInSecondCurrency') < 0)
                    return $this->validator->errors()->add('payer',
                            'Недостаточно средств на счету плательщика - '
                            . $payerAccountSecondCurrency->balance . ' '
                            . $payerAccountSecondCurrency->currency->isoName) . '.';

            }
        }

        $this->validationPolicy->apply();
    }
}
