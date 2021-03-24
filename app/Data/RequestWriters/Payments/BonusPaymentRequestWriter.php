<?php
/**
 * @author Sultonazar Mamadazizov <sultonazar.mamadazizov@mail.ru>
 */

namespace App\Data\RequestWriters\Payments;


class BonusPaymentRequestWriter extends PaymentRequestWriter
{
    function getPayerAccounts()
    {
    }

    function getSubjectAccount($subject, $currencyId){
        return $subject->accounts()->where('currency_id', $currencyId)->firstOrFail();
    }
}
