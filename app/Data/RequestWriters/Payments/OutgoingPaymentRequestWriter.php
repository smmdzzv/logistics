<?php

namespace App\Data\RequestWriters\Payments;

use App\Models\Till\Account;

class OutgoingPaymentRequestWriter extends PaymentRequestWriter
{
    private $recipient;

    public function __construct($recipient, $input)
    {
        $this->recipient = $recipient;
        parent::__construct($input);
    }

    function write()
    {
        parent::write();

        $this->updateAccountFromAndTo();

        if ($this->saved->payment->status === 'completed')
            $this->updateAccountsBalance();

        return $this->saved;
    }

    /**
     *Sets payment account.
     */
    private function updateAccountFromAndTo()
    {
//        if(!isset($this->data->duobAccount))
//            $this->data->duobAccount = LegalEntity::first()->accounts()->with('currency')->first();
        $this->data->accountFrom = Account::with('currency')->findOrFail($this->input->payment['accountFrom']);
        $this->saved->payment->recipient_id = $this->recipient->id;
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
