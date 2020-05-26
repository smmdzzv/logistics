<?php


namespace App\Services\Till\Payment\PaymentAccount;


class TransferBetweenBranchAccountsService extends DefaultPaymentAccountsService
{
    protected function getPayerAccounts()
    {
        $this->paymentAccountsDto->payerAccountInSecondCurrency =
            $this->payment->payer->accounts()
                ->where('currency_id', $this->payment->secondPaidCurrency->id)
                ->firstOrFail();
    }

    protected function getPayeeAccounts()
    {
        $this->paymentAccountsDto->payeeAccountInBillCurrency =
            $this->payment->payee->accounts()
                ->where('currency_id', $this->payment->billCurrency->id)
                ->firstOrFail();
    }
}
