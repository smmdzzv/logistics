<?php


namespace App\Data\RequestWriters\Order;


use App\Data\RequestWriters\RequestWriter;
use App\Models\Currency;
use App\Models\Order;
use App\Models\StoredItems\ItemsSelection;
use App\Models\StoredItems\StorageHistory;
use App\Models\StoredItems\StoredItem;
use App\Models\Till\Account;
use App\Models\Till\Payment;
use App\Models\Till\PaymentItem;
use App\Models\Users\TrustedUser;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Ramsey\Collection\Collection;

/**@deprecated*/
class DeliverOrderItemsRequestWriter extends RequestWriter
{
    private $storedItems;
    private Order $order;

    private Account $clientAccount;

    private Payment $payment;

    public function __construct($request, Order $order)
    {
        parent::__construct(null);

        $this->request = $request;
        $this->storedItems = StoredItem::whereIn('id', \request()->get('items'))->get();
        $this->order = $order;
    }

    /**
     * @return Payment
     */
    function write()
    {
        $this->checkPayment();

        $this->deliverItems();

        $this->changeOrderStatus();

        return $this->payment;
    }

    private function checkPayment()
    {
        //Filter unpaid items
        $ids = $this->storedItems->map(function ($item) {
            return $item->id;
        });

        $unpaidItems = StoredItem::whereIn('id', $ids)->unpaid()->get();

        //Calculate bill
        $unpaidStoredItemInfos = $unpaidItems->load('info.billingInfo')
            ->map(function ($item) {
                return $item->info;
            });

        $paymentSum = $unpaidStoredItemInfos->sum(function ($info) {
            return $info->billingInfo->pricePerItem;
        });

        if ($paymentSum > 0) {
            $this->clientAccount = $this->order->owner->accounts()->dollarAccount();

            if ($this->clientAccount->balance < $paymentSum) {
                if (!$this->request->get('isDebtRequested'))
                    abort(422, "Недостаточно средств на балансе");
                else if (!$this->checkForDebtPossibility($paymentSum))
                    abort(422, "Доверительный платеж не возможен");
            }

            $this->createPayment($paymentSum, $unpaidItems);
        }
    }

    private function checkForDebtPossibility($paymentSum): bool
    {
        $trusted = TrustedUser::where('user_id', $this->order->owner->id)
            ->where('to', '>=', Carbon::now()->toDateString())
            ->first();

        if (!$trusted)
            return false;

        $overallDebt = $this->clientAccount->balance - $paymentSum;

        if (abs($overallDebt) > $trusted->maxDebt)
            abort(400,
                "Суммарный долг доверенного клиента превыщает максимально допустимый.
                 Текущая задолженность " . abs($overallDebt) . " USD.
                 Из них стоимость заказа " . $paymentSum . " USD"
            );

        return true;
    }

    private function createPayment($paymentSum, Collection $unpaidStoredItems)
    {
        $dollar = Currency::where('isoName', 'USD')->first()->id;

        $itemsSelection = ItemsSelection::create([
            'name' => 'Заказ ' . $this->order->owner->code
        ]);

        $itemsSelection->sync($unpaidStoredItems->pluck('id'));

        $this->payment = new Payment([
            'branch_id' => auth()->user()->branch->id,
            'cashier_id' => auth()->user()->id,
            'status' => 'completed',
            'prepared_by_id' => null,
            'payer_id' => $this->order->owner->id,
            'payer_account_in_bill_currency_id' => $this->clientAccount->id,
            'payer_account_in_second_currency_id' => null,
            'payer_type' => 'user',
            'payee_id' => null,
            'payee_account_in_bill_currency_id' => null,
            'payee_account_in_second_currency_id' => null,
            'payee_type' => null,
            'payment_item_id' => PaymentItem::firstOrCreate([
                'title' => 'Списание с баланса',
                'description' => 'Списание денег с баланса клиента в счет оплаты заказы'
            ])->id,
            'billAmount' => $paymentSum,
            'paidAmountInBillCurrency' => $paymentSum,
            'paidAmountInSecondCurrency' => 0,
            'bill_currency_id' => $dollar,
            'second_paid_currency_id' => null,
            'exchange_rate_id' => null,
            'comment' => 'Списание денег с баланса в счет оплаты заказа',
            'client_items_selection_id' => $itemsSelection->id
        ]);

        $this->payment->fillExtras();
        $this->payment->clientDebt -= $this->payment->billAmount;
        $this->payment->placesLeft -= $unpaidStoredItems->count();
        $this->payment->saveOrFail();

        $this->clientAccount->balance -= $paymentSum;
        $this->clientAccount->save();


//
//        $unpaidStoredItems->each(function ($item, $key) use ($orderPayment) {
//            OrderPaymentItem::create([
//                'stored_item_id' => $item->id,
//                'order_payment_id' => $orderPayment->id
//            ]);
//        });
    }

    private function deliverItems()
    {
        $ids = $this->storedItems->map(function ($item) {
            return $item->id;
        });


        StorageHistory::whereHas('storedItem', function (Builder $query) use ($ids) {
            $query->whereIn('id', $ids->all());
        })->update([
            'deleted_at' => Carbon::now(),
            'deletedById' => auth()->user()->id
        ]);

        StoredItem::whereIn('id', $ids->all())->update([
            'deleted_at' => Carbon::now(),
            'deleted_by_id' => auth()->user()->id
        ]);
    }

    private function changeOrderStatus()
    {
        if ($this->order->storedItems()->count() === 0)
            $this->order->complete();
    }

}
