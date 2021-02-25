<?php
/**
 * @author Sultonazar Mamadazizov <sultonazar.mamadazizov@mail.ru>
 */

namespace App\Http\Requests\Till;


use App\User;

class PaymentSubjectsValidationPolicy extends PaymentValidationPolicy
{
    public function apply()
    {
        if (!$this->payer)
            return $this->validator->errors()->add('payer', 'Указанный плательщик не найден. ');

        if (!$this->payee)
            return $this->validator->errors()->add('payee', 'Указанный получатель не найден. ');

        if ($this->payer instanceof User && $this->payee instanceof User)
            return $this->validator->errors()->add('payer', 'Перевод денег между клиентами запрещен. ');

        $this->validationPolicy->apply();
    }

}
