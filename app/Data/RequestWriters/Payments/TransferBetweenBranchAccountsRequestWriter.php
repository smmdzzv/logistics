<?php


namespace App\Data\RequestWriters\Payments;


class TransferBetweenBranchAccountsRequestWriter extends PaymentRequestWriter
{
//    protected function getAccounts(){
//        parent::getAccounts();
//
//        $this->payeeAccount = $this->payee->accounts()->where('currency_id', $this->request->get('billCurrency'))->firstOrFail();
//    }

    protected function getPayerAccounts()
    {
        $this->payerAccountInSecondCurrency = $this->payer->accounts()->where('currency_id', $this->request->get('secondPaidCurrency'))->firstOrFail();
    }

    protected function getPayeeAccounts()
    {
        $this->payeeAccountInBillCurrency = $this->payee->accounts()->where('currency_id', $this->request->get('billCurrency'))->firstOrFail();
    }

    protected function updatePayeeBalance()
    {
        if ($this->payeeAccountInBillCurrency) {
            $this->payeeAccountInBillCurrency->balance += $this->payment->billAmount;
            $this->payeeAccountInBillCurrency->save();
        }
    }
}
