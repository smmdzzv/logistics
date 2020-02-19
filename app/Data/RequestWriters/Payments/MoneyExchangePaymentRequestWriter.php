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
            ->where('currency_id', $this->request->get('billCurrency'))
            ->firstOrFail();

        $this->outgoingPayment = Payment::create([
            'branch_id' => auth()->user()->branch->id,
            'cashier_id' => auth()->user()->id,
            'status' => 'completed',
            'payer_id' => $this->payee->id,
            'payer_account_id' => $this->outgoingAccount->id,
            'payer_type' => $this->request->get('payee_type'),
            'payee_id' => $this->request->get('payer'),
            'payee_account_id' => null,
            'payee_type' => $this->request->get('payer_type'),
            'payment_item_id' => $this->request->get('paymentItem'),
            'billAmount' => $this->request->get('billAmount'),
            'paidAmount' => $this->request->get('billAmount'),
            'bill_currency_id' => $this->request->get('billCurrency'),
            'paid_currency_id' => $this->request->get('billCurrency'),
            'exchange_rate_id' => null,
            'comment' => "Выдача наличных от связанной операции",
        ]);
    }

    private function updateBranchBalance()
    {
        $this->outgoingAccount->balance -= $this->request->get('billAmount');
        $this->outgoingAccount->save();
    }
}
