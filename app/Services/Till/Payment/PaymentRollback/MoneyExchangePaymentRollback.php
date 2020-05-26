<?php


namespace App\Services\Till\Payment\PaymentRollback;


use App\Models\Till\Account;
use App\Models\Till\Payment;
use App\Services\Till\Account\AccountService;

class MoneyExchangePaymentRollback extends DefaultPaymentRollback
{
    public function rollback()
    {
        parent::rollback();

        if ($this->payment->status === 'completed') {
            $this->payment->relatedPayments->each(function (Payment $related) {
                $related->delete();
            });

            $this->putToBranchAccount();
        }
    }

    public function putToBranchAccount()
    {

        $account = $this->payment->payee->accounts()
            ->where('currency_id', $this->payment->billCurrency)
            ->first();

        if ($account) {
            $service = new AccountService();

            /** @var Account $account */
            $service->putMoney($account, $this->payment->paidAmountInBillCurrency);
        }
    }
}
