<?php


namespace App\Data\RequestWriters\Payments;


class BalanceReplenishmentPaymentRequestWriter extends PaymentRequestWriter
{
    protected function getAccounts()
    {
        //Users have only dollar account. All operations should be in dollars
        $this->payerAccount = $this->payer->accounts()->dollarAccount();
        if (!$this->payerAccount)
            abort('400', 'Пользователь не имеет долларового счета');

        $this->payeeAccount = $this->getSubjectAccount($this->payee);
    }

    protected function updatePayerBalance()
    {
        $this->payerAccount->balance += $this->payment->paidAmount;
        $this->payerAccount->save();
    }

}
