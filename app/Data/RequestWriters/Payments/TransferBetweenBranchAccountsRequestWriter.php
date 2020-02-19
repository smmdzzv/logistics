<?php


namespace App\Data\RequestWriters\Payments;


class TransferBetweenBranchAccountsRequestWriter extends PaymentRequestWriter
{
    protected function getAccounts(){
        parent::getAccounts();

        $this->payeeAccount = $this->payee->accounts()->where('currency_id', $this->request->get('billCurrency'))->firstOrFail();
    }

    protected function updatePayeeBalance()
    {
        if ($this->payeeAccount) {
            $this->payeeAccount->balance += $this->payment->billAmount;
            $this->payeeAccount->save();
        }
    }
}
