<?php


namespace App\Services\Till\Payment\PaymentRollback;


class TransferBetweenBranchAccountsPaymentRollback extends DefaultPaymentRollback
{
    protected function withdrawMoneyFromPayeeAccounts()
    {
        if ($this->paymentAccounts->payeeAccountInBillCurrency) {
            $this->accountService->withdrawMoney(
                $this->paymentAccounts->payeeAccountInBillCurrency,
                $this->payment->billAmount
            );
        }
    }
}
