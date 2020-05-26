<?php


namespace App\Services\Till\Payment\PaymentAccount;


class BalanceReplenishmentPaymentAccountsService extends DefaultPaymentAccountsService
{
    function getPayerAccounts()
    {
        $this->paymentAccountsDto->payerAccountInBillCurrency = $this->payment->payer->accounts()->dollarAccount();
    }
}
