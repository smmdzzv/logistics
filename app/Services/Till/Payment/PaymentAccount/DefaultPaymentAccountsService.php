<?php

namespace App\Services\Till\Payment\PaymentAccount;

use App\Data\Dto\Payment\PaymentAccountsDto;
use App\Models\Branch;
use App\Models\Till\Payment;
class DefaultPaymentAccountsService implements PaymentAccountService
{
    protected PaymentAccountsDto $paymentAccountsDto;

    protected Payment $payment;

    public function __construct($payment)
    {
        $this->payment = $payment;
        $this->paymentAccountsDto = new PaymentAccountsDto();
    }

    public function getPaymentAccounts(): PaymentAccountsDto
    {
        $this->getPayerAccounts();
        $this->getPayeeAccounts();
        return $this->paymentAccountsDto;
    }

    protected function getPayerAccounts()
    {
        $this->getSubjectAccounts($this->payment->payer,
            $this->paymentAccountsDto->payerAccountInBillCurrency,
            $this->paymentAccountsDto->payerAccountInSecondCurrency);
    }

    protected function getPayeeAccounts()
    {
        $this->getSubjectAccounts($this->payment->payee,
            $this->paymentAccountsDto->payeeAccountInBillCurrency,
            $this->paymentAccountsDto->payeeAccountInSecondCurrency);
    }

    protected function getSubjectAccounts($owner, &$accountInBillCurrency, &$accountInSecondCurrency)
    {
        $accountInBillCurrency = $this->getSubjectAccount($owner, $this->payment->billCurrency->id);
        if ($this->payment->secondPaidCurrency && $this->payment->paidAmountInSecondCurrency > 0){
            $accountInSecondCurrency = $this->getSubjectAccount($owner, $this->payment->secondPaidCurrency->id);
        }
    }

    private function getSubjectAccount($subject, $currencyId)
    {
        $account = null;

        if ($subject instanceof Branch) {
            $account = $subject->accounts()->where('currency_id', $currencyId)->firstOrFail();
        }

        return $account;
    }
}
