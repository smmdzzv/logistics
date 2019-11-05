<?php


namespace App\Data\RequestWriters\Payments;


use App\Data\RequestWriters\RequestWriter;
use App\Models\LegalEntities\LegalEntity;
use App\Models\Till\MoneyExchange;
use App\Models\Till\Payment;
use App\Models\Till\PaymentItem;
use stdClass;

class ExchangeMoneyRequestWriter extends RequestWriter
{
    /**
     * @return stdClass which contains saved models
     */
    function write()
    {
        $this->prepareData();

        $this->writeOff();

        $this->deposit();

        return $this->saved;
    }

    public function prepareData()
    {
        $this->data->rate = MoneyExchange::where('from', $this->input->from)
            ->where('to', $this->input->to)
            ->firstOrFail();

        $this->data->duob = LegalEntity::first();

        $this->data->accountFrom = $this->data->duob->accounts()
            ->where('currencyId', $this->data->to)
            ->firstOrFail();

        $this->data->accountTo = $this->data->duob->accounts()
            ->where('currencyId', $this->data->from)
            ->firstOrFail();

        $this->data->paymentItemIn = PaymentItem::where('title', 'Прием наличных')->firstOrFail();

        $this->data->paymentItemOut = PaymentItem::where('title', 'Выдача наличных')->firstOrFail();
    }

    public function writeOff()
    {
        $this->data->accountFrom->balance -= $this->input->amount;
        $this->data->accountFrom->save();

        $this->saved->paymetOut = Payment::create([
            'branchId' => $this->input->cashier->branch->id,
            'cashierId' => $this->input->cashier->id,
            'currencyId' => $this->data->rate->to,
            'paymentItemId' => $this->data->paymentItemOut->id,
            'accountFromId' => $this->data->accountFrom->id,
            'amount' => $this->input->amount,
            'status' => 'completed',
            'comment' => 'Обмен валют (списание)'
        ]);
    }

    public function deposit(){
        $this->data->accountTo->balance += $this->input->amount;
        $this->data->accountTo->save();

        $amount = $this->input->amount * $this->data->rate->coefficient;

        $this->saved->paymetOut = Payment::create([
            'branchId' => $this->input->cashier->branch->id,
            'cashierId' => $this->input->cashier->id,
            'currencyId' => $this->data->rate->from,
            'paymentItemId' => $this->data->paymentItemIn->id,
            'accountToId' => $this->data->accountTo->id,
            'exchangeId' => $this->data->rate->id,
            'amount' => round($amount, 2),
            'status' => 'completed',
            'comment' => 'Обмен валют (зачисление)'
        ]);
    }
}
