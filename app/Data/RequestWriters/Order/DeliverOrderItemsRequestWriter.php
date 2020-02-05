<?php


namespace App\Data\RequestWriters\Order;


use App\Data\RequestWriters\RequestWriter;
use App\Models\Currency;
use App\Models\Order\OrderPayment;
use App\Models\Order\OrderPaymentItem;
use App\Models\StoredItems\StoredItem;
use App\Models\Till\Payment;
use App\Models\Till\PaymentItem;
use App\Models\Users\TrustedUser;
use App\StoredItems\StorageHistory;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;

class DeliverOrderItemsRequestWriter extends RequestWriter
{
    function write()
    {
        $this->checkPayment();

        $this->deliverItems();

        $this->changeOrderStatus();

        return $this->saved;
    }

    private function checkPayment()
    {
        //Filter unpaid items

        $ids = $this->input->storedItems->map(function ($item) {
            return $item->id;
        });

//        $paidItemsIds = OrderPaymentItem::whereIn('stored_item_id', $ids)
//            ->get()
//            ->map(function ($item) {
//                return $item->stored_item_id;
//            });

//        $unpaidItems = $this->input->storedItems
//            ->filter(function ($storedItemId, $key) use ($paidItemsIds) {
//                return !$paidItemsIds->contains($storedItemId);
//            });

        $unpaidItems = StoredItem::whereIn('id', $ids)->get();

        //Calculate bill
        $unpaidStoredItemInfos = $unpaidItems->load('info.billingInfo')
            ->map(function ($item) {
                return $item->info;
            });

        $paymentSum = $unpaidStoredItemInfos->sum(function ($info) {
            return $info->billingInfo->pricePerItem;
        });

//        $unpaidStoredItemInfos->each(function (StoredItemInfo $info, $key) use ($paymentSum) {
//            $paymentSum += $info->billingInfo->pricePerItem;
//
//        });

        if ($paymentSum > 0) {
            $this->data->account = $this->input->order->owner->accounts()->first();

            if ($this->data->account->balance < $paymentSum) {
                if (!$this->input->isDebtRequested)
                    abort(400, "Недостаточно средств на балансе");
                else if (!$this->checkForDebtPossibility($paymentSum))
                    abort(400, "Доверительный платеж не возможен");
            }

            $this->createPayment($paymentSum, $unpaidItems);
        }


//        $order = $this->input->order;
//        $this->data->client = $order->owner;
//
//        if (!$order->payment) {
//
//            $this->data->account = $this->data->client->accounts()->first();
//
//            if ($this->data->account->balance < $order->totalPrice
//                && !$this->checkForDebtPossibility())
//                abort(400, "Недостаточно средств на балансе");
//            else
//                $this->createPayment();
//        }
    }

    private function checkForDebtPossibility($paymentSum): bool
    {
        $trusted = TrustedUser::where('user_id', $this->input->order->owner->id)->where('to', '>=', Carbon::now()->toDateString())
            ->first();

        if (!$trusted)
            return false;

        $overallDebt = $this->data->account->balance - $paymentSum;

        if (abs($overallDebt) > $trusted->maxDebt)
            abort(400,
                "Суммарный долг доверенного клиента превыщает максимально допустимый.
                 Текущая задолженность " . abs($overallDebt) . " USD. 
                 Из них стоимость заказа " . $paymentSum . " USD"
            );

        return true;
    }

    private function createPayment($paymentSum, $unpaidStoredItems)
    {

        $this->saved->payment = Payment::create([
            'branchId' => $this->input->employee->branch->id,
            'cashierId' => $this->input->employee->id,
            'currencyId' => Currency::where('isoName', 'USD')->first()->id,
            'payerId' => $this->input->order->owner->id,
            'preparedById' => auth()->user()->id,
            'paymentItemId' => PaymentItem::firstOrCreate([
                'title' => 'Списание с баланса',
                'type' => 'internal',
                'description' => 'Списание денег с баланса клиента в счет оплаты заказы'
            ])->id,
            'accountFromId' => $this->data->account->id,
            'amount' => $paymentSum,
            'status' => 'completed',
            'comment' => 'Списание денег с баланса в счет оплаты заказа'
        ]);

//        $this->data->account->balance -= $this->input->order->totalPrice;
        $this->data->account->balance -= $paymentSum;
        $this->data->account->save();

        $orderPayment = OrderPayment::create([
            'order_id' => $this->input->order->id,
            'payment_id' => $this->saved->payment->id
        ]);

        $unpaidStoredItems->each(function ($item, $key) use ($orderPayment) {
            OrderPaymentItem::create([
                'stored_item_id' => $item->id,
                'order_payment_id' => $orderPayment->id
            ]);
        });

//        $this->input->order->paymentId = $this->saved->payment->id;
//        $this->input->order->save();
    }

    private function deliverItems()
    {
        $ids = $this->input->storedItems->map(function ($item) {
            return $item->id;
        });


        StorageHistory::whereHas('storedItem', function (Builder $query) use ($ids) {
            $query->whereIn('id', $ids->all());
        })->update([
            'deleted_at' => Carbon::now(),
            'deletedById' => $this->input->employee->id
        ]);

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
