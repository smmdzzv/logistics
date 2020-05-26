<?php


namespace App\Services\Till\Payment\PaymentRollback;


class BalanceReplenishmentPaymentRollback extends DefaultPaymentRollback
{
    public function rollback()
    {
        parent::rollback();

        if ($this->payment->status === 'completed' && $this->paymentAccounts->payerAccountInBillCurrency) {
            $this->accountService->withdrawMoney(
                $this->paymentAccounts->payerAccountInBillCurrency,
                $this->payment->billAmount
            );
        }
    }

    protected function putMoneyToPayerAccounts()
    {

    }
}
