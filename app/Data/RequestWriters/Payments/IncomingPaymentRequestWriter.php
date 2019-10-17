<?php

namespace App\Data\RequestWriters\Payments;

use App\Models\LegalEntities\LegalEntity;
use App\Models\Till\MoneyExchange;
use App\Models\Till\PaymentItem;
use App\User;

class IncomingPaymentRequestWriter extends PaymentRequestWriter
{

    public function __construct($input)
    {
        parent::__construct($input);
    }

    function write()
    {
        parent::write();

        $this->updateAccountTo();
        $this->updateAccountsBalance();
        return $this->saved;
    }

    /**
     *Sets payment destination account.
     */
    private function updateAccountTo()
    {
        $this->data->item = PaymentItem::find($this->input->payment['paymentItemId']);
        $this->data->duobAccount = LegalEntity::first()->accounts()->first();
        $this->data->userAccount = User::find($this->input->payment['payerId'])->accounts()->first();

        if ($this->data->item->title === 'Пополнение баланса')
            $this->saved->payment->accountToId = $this->data->userAccount->id;
        else
            $this->saved->payment->accountToId = $this->data->duobAccount->id;

        $this->saved->payment->save();
    }

    /**
     *Updates related accounts balance. Duobs` balance is always updated
     */
    private function updateAccountsBalance()
    {
        $sum = $this->saved->payment->amount;

        $exchange = MoneyExchange::find($this->saved->payment->exchangeId);
        if ($exchange)
            $sum = $sum * $exchange->coefficient;

        $this->data->duobAccount->balance += $sum;
        $this->data->duobAccount->save();

        if ($this->data->item->title === 'Пополнение баланса') {
            $this->data->userAccount->balance += $sum;
            $this->data->userAccount->save();
        }
    }
}
