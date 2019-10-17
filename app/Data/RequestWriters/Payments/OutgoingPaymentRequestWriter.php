<?php

namespace App\Data\RequestWriters\Payments;

use App\Models\LegalEntities\LegalEntity;

class OutgoingPaymentRequestWriter extends PaymentRequestWriter
{

    public function __construct($input)
    {
        parent::__construct($input);
    }

    function write()
    {
        parent::write();

        $this->updateAccountFrom();
        $this->updateAccountsBalance();
        return $this->saved;
    }

    /**
     *Sets payment account.
     */
    private function updateAccountFrom()
    {
        if(!isset($this->data->duobAccount))
            $this->data->duobAccount = LegalEntity::first()->accounts()->with('currency')->first();
        $this->saved->payment->accountFromId = $this->data->duobAccount->id;

        $this->saved->payment->save();
    }

    /**
     *Updates related accounts balance. Outgoing payment
     * doesn`t support money exchange
     */
    private function updateAccountsBalance()
    {
        $sum = $this->saved->payment->amount;
        $this->data->duobAccount->balance -= $sum;
        $this->data->duobAccount->save();
    }
}
