<?php

namespace App\Services\Till\Payment\PaymentRollback;

use App\Data\Dto\Payment\PaymentAccountsDto;
use App\Models\Till\Payment;
use App\Services\Till\Account\AccountService;

class DefaultPaymentRollback
{
    protected Payment $payment;

    protected PaymentAccountsDto $paymentAccounts;

    protected AccountService $accountService;

    public function __construct(Payment $payment, PaymentAccountsDto $paymentAccounts, AccountService $accountService)
    {
        $this->payment = $payment;
        $this->paymentAccounts = $paymentAccounts;
        $this->accountService = $accountService;
    }

    public function rollback()
    {
        if ($this->payment->status === 'completed') {
            $this->withdrawMoneyFromPayeeAccounts();
            $this->transferMoneyToPayerAccounts();
        }
        $this->payment->delete();
    }

    private function withdrawMoneyFromPayeeAccounts()
    {
        if ($this->paymentAccounts->payeeAccountInBillCurrency) {
            $this->accountService->withdrawMoney(
                $this->paymentAccounts->payeeAccountInBillCurrency,
                $this->payment->paidAmountInBillCurrency
            );
        }

        if ($this->paymentAccounts->payeeAccountInSecondCurrency) {
            $this->accountService->withdrawMoney(
                $this->paymentAccounts->payeeAccountInSecondCurrency,
                $this->payment->paidAmountInSecondCurrency
            );
        }
    }

    private function transferMoneyToPayerAccounts()
    {
        if ($this->paymentAccounts->payerAccountInBillCurrency) {
            $this->accountService->putMoney(
                $this->paymentAccounts->payerAccountInBillCurrency,
                $this->payment->paidAmountInBillCurrency
            );
        }

        if ($this->paymentAccounts->payerAccountInSecondCurrency) {
            $this->accountService->putMoney(
                $this->paymentAccounts->payerAccountInSecondCurrency,
                $this->payment->paidAmountInSecondCurrency
            );
        }
    }
}
