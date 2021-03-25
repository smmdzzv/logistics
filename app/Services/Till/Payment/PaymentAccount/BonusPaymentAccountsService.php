<?php
/**
 * @author Sultonazar Mamadazizov <sultonazar.mamadazizov@mail.ru>
 */

namespace App\Services\Till\Payment\PaymentAccount;


use App\Models\Currency;

class BonusPaymentAccountsService extends DefaultPaymentAccountsService
{
    protected function getPayeeAccounts()
    {
        $this->paymentAccountsDto->payeeAccountInBillCurrency
            = $this->payment->payee->accounts()->whereHas('currency', function ($query){
                $query->where('isoName', 'USD');
        })->firstOrFail();
    }

    protected function getPayerAccounts(){

    }
}
