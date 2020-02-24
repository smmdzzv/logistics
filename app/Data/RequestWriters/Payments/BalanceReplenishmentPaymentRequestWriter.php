<?php


namespace App\Data\RequestWriters\Payments;


class BalanceReplenishmentPaymentRequestWriter extends PaymentRequestWriter
{
//    protected function getAccounts()
//    {
//        parent::getAccounts();
//
//        $this->payeeAccountInSecondCurrency = null;
//        $this->payeeAccountInBillCurrency =
//        //Users have only dollar account. All operations should be in dollars
//        $this->payerAccount = $this->payer->accounts()->dollarAccount();
//        if (!$this->payerAccount)
//            abort('400', 'Пользователь не имеет долларового счета');
//
//        $this->payeeAccount = $this->getSubjectAccount($this->payee);
//    }

    //Users have only dollar account. All operations should be in dollars
    protected function getPayerAccounts()
    {
        $this->payerAccountInBillCurrency = $this->payer->accounts()->dollarAccount();
        if (!$this->payerAccountInBillCurrency)
            abort('422', 'Пользователь не имеет долларового счета');
    }


    protected function checkPayerBalance()
    {
        return;
    }

    protected function updatePayerBalance()
    {
        $this->payerAccountInBillCurrency->balance += $this->payment->billAmount;
        $this->payerAccountInBillCurrency->save();
    }

}
