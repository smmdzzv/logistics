<?php


namespace App\Data\RequestWriters\Order;


use App\Data\RequestWriters\Payments\IncomingPaymentRequestWriter;
use App\Data\RequestWriters\RequestWriter;
use App\Models\Currency;
use App\Models\StoredItems\StoredItem;
use App\Models\StoredItems\StoredItemTripHistory;
use App\Models\Till\Payment;
use App\Models\Till\PaymentItem;
use Carbon\Carbon;
use stdClass;

class DeliverOrderItemsRequestWriter extends RequestWriter
{
    public function __construct($input)
    {
        parent::__construct($input);
    }

    function write()
    {
        $this->checkPayment();

        $this->deliverItems();

        $this->changeOrderStatus();

        return $this->saved;
    }

    private function checkPayment()
    {
        $order = $this->input->order;
        $this->data->client = $order->owner;

        if (!$order->payment) {
            $this->data->account = $this->data->client->accounts()->first();
            if ($this->data->account->balance < $order->totalPrice)
                abort(400, "Недостаточно средств на балансе");
            else
                $this->createPayment();
        }
    }

    private function createPayment()
    {

        $this->saved->payment = Payment::create([
            'branchId' => $this->input->employee->branch->id,
            'cashierId' => $this->input->employee->id,
            'currencyId' => Currency::where('isoName', 'USD')->first()->id,
            'payerId' => $this->data->client->id,
            'paymentItemId' => PaymentItem::firstOrCreate([
                'title' => 'Оплата заказа',
                'type' => 'internal',
                'description' => 'Списание денег с баланса клиента в счет оплаты заказы'
            ])->id,
            'accountFromId' => $this->data->account->id,
            'amount' => $this->input->order->totalPrice,
            'comment' => 'Списание денег с баланса в счет оплаты заказа'
        ]);

        $this->data->account->balance -= $this->input->order->totalPrice;
        $this->data->account->save();

        $this->input->order->paymentId = $this->saved->payment->id;
        $this->input->order->save();
    }

    private function deliverItems()
    {
        $ids = $this->input->storedItems->map(function ($item) {
            return $item->id;
        });

        StoredItem::whereIn('id', $ids->all())->update([
            'deleted_at' => Carbon::now(),
            'deleted_by_id' => $this->input->employee->id
        ]);
    }

    private function changeOrderStatus()
    {
        if ($this->input->order->storedItems()->count() === 0)
            $this->input->order->complete();
    }

}
