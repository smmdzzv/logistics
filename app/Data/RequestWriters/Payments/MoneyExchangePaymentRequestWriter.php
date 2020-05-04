<?php


namespace App\Data\RequestWriters\Payments;


use App\Models\Till\Account;
use App\Models\Till\Payment;

class MoneyExchangePaymentRequestWriter extends PaymentRequestWriter
{
    private Payment $outgoingPayment;
    private Account $outgoingAccount;

    public function write()
    {
        parent::write();

        if ($this->payment->status === 'completed') {
            $this->createOutgoingPayment();
            $this->updateBranchBalance();
        }

        return $this->payment;
    }

    /**
     * Creates outgoing payment with exchanged amount in target currency
     * Branch is payee in incoming payment, while user is payer.
     */
    private function createOutgoingPayment()
    {
        $this->outgoingAccount = $this->payee->accounts()
            ->where('currency_id', $this->request['billCurrency'])
            ->firstOrFail();

        $this->outgoingPayment = Payment::create([
            'branch_id' => auth()->user()->branch->id,
            'cashier_id' => auth()->user()->id,
            'status' => 'completed',
            'prepared_by_id' => null,
            'payer_id' => $this->payee->id,
            'payer_account_in_bill_currency_id' => $this->outgoingAccount->id,
            'payer_account_in_second_currency_id' => null,
            'payer_type' => $this->request['payee_type'],
            'payee_id' => $this->request['payer'],
            'payee_account_in_bill_currency_id' => null,
            'payee_account_in_second_currency_id' => null,
            'payee_type' => $this->request['payer_type'],
            'payment_item_id' => $this->request['paymentItem'],
            'billAmount' => $this->request['billAmount'],
            'paidAmountInBillCurrency' => $this->request['billAmount'],
            'paidAmountInSecondCurrency' => 0,
            'bill_currency_id' => $this->request['billCurrency'],
            'second_paid_currency_id' => null,
            'related_payment_id' => $this->payment->id,
            'exchange_rate_id' => null,
            'comment' => "Выдача наличных от связанной операции",
        ]);
    }

    private function updateBranchBalance()
    {
        $this->outgoingAccount->balance -= $this->request['billAmount'];
        $this->outgoingAccount->save();
    }
}
