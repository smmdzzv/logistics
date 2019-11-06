<?php

namespace App\Data\RequestWriters\Payments;

use App\Models\Till\Account;

class OutgoingPaymentRequestWriter extends PaymentRequestWriter
{

    function write()
    {
        parent::write();

        $this->updateAccountFrom();

        if ($this->saved->payment->status === 'completed')
            $this->updateAccountsBalance();

        return $this->saved;
    }

    /**
     *Sets payment account.
     */
    private function updateAccountFrom()
    {
//        if(!isset($this->data->duobAccount))
//            $this->data->duobAccount = LegalEntity::first()->accounts()->with('currency')->first();
        $this->data->accountFrom = Account::with('currency')->findOrFail($this->input->payment['accountFrom']);

        $this->saved->payment->accountFromId = $this->data->accountFrom->id;
        $this->saved->payment->save();
    }

    /**
     *Updates related accounts balance. Outgoing payment
     * doesn`t support money exchange
     */
    private function updateAccountsBalance()
    {
        $sum = $this->saved->payment->amount;
        $this->data->accountFrom->balance -= $sum;
        $this->data->accountFrom->save();
    }
}
