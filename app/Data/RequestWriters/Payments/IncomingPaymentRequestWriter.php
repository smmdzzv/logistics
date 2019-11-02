<?php

namespace App\Data\RequestWriters\Payments;

use App\Models\Currency;
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

        if ($this->saved->payment->status === 'completed')
            $this->updateAccountsBalance();

        return $this->saved;
    }

    /**
     *Sets payment destination account.
     */
    private function updateAccountTo()
    {
        $this->data->item = PaymentItem::find($this->input->payment['paymentItemId']);
        $this->data->duobAccount = LegalEntity::first()->accounts()->where('id', $this->input->payment['accountTo'])->first();
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

        $this->data->exchange = MoneyExchange::find($this->saved->payment->exchangeId);
        if ($this->data->exchange)
            $sum = $sum * $this->data->exchange->coefficient;

        $this->data->duobAccount->balance += $sum;
        $this->data->duobAccount->save();

        if ($this->data->item->title === 'Пополнение баланса')
            $this->updateUsersAccountBalance();
    }

    private function updateUsersAccountBalance()
    {
        $usd = Currency::where('isoName', 'USD')->first();
        $sum = $this->saved->payment->amount;

        if ($this->saved->payment->currency->id !== $usd->id) {
            $exchange = $this->data->exchange;
            if ($this->data->exchange->to !== $usd->id)
                $exchange = MoneyExchange::where('from', $this->saved->payment->currency->id)
                    ->where('to', $usd->id)->latest()->first();

            $sum = $sum * $exchange->coefficient;
        }

        $this->data->userAccount->balance += $sum;
        $this->data->userAccount->save();
    }
}
