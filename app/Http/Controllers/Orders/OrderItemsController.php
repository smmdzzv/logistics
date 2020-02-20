<?php

namespace App\Http\Controllers\Orders;

use App\Data\RequestWriters\Order\DeliverOrderItemsRequestWriter;
use App\Models\Currency;
use App\Models\LegalEntities\LegalEntity;
use App\Models\Order;
use App\Models\Order\OrderPayment;
use App\Models\Order\OrderPaymentItem;
use App\Models\StoredItems\StoredItem;
use App\Http\Controllers\Controller;
use App\Models\Till\Payment;
use App\Models\Till\PaymentItem;
use App\Models\Users\TrustedUser;
use Carbon\Carbon;
use Illuminate\Http\Request;

class OrderItemsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('roles.deny:client,driver,worker');
    }

    public function edit()
    {
        return view('orders.edit-items-list');
    }

    public function deliver(Order $order, Request $request)
    {
        $writer = new DeliverOrderItemsRequestWriter($request, $order);
        $payment = $writer->write();
        return $payment->id;
    }

    //TODO refactor this and DeliverOrderItemsRequestWriter
    public function storePaymentRequest(Order $order)
    {
        $items = StoredItem::whereIn('id', \request()->input('items'))->unpaid()->get();

        //Calculate bill
        $unpaidStoredItemInfos = $items->load('info.billingInfo')
            ->map(function ($item) {
                return $item->info;
            });

        $paymentSum = $unpaidStoredItemInfos->sum(function ($info) {
            return $info->billingInfo->pricePerItem;
        });

        $paymentSum -= $order->owner->accounts()->dollarAccount()->balance;

        if (request('isDebtRequested')) {
            $trusted = TrustedUser::where('user_id', $order->owner->id)->where('to', '>=', Carbon::now()->toDateString())
                ->first();

            if ($trusted) {
                $paymentSum -= $trusted->maxDebt;
            }
        }

        if ($paymentSum <= 0) {
            return abort(400, 'На балансе клиента достаточно денег для оплаты выбранных товаров');
        }

        $payment = Payment::create([
            'branchId' => auth()->user()->branch->id,
            'cashierId' => auth()->user()->id,
            'currencyId' => Currency::where('isoName', 'USD')->first()->id,
            'payerId' => $order->owner->id,
            'paymentItemId' => PaymentItem::where('title', 'Пополнение баланса')->first()->id,
            'preparedById' => auth()->user()->id,
//            'accountToId' => LegalEntity::first()->accounts()->whereHas('currency', function (Builder $query) {
//                $query->where('isoName', 'USD');
//            })->first()->id,
            'accountToId' => LegalEntity::first()->accounts()->dollarAccount()->id,
            'amount' => $paymentSum,
            'status' => 'pending',
            'comment' => 'Пополнение баланса для оплаты заказа'
        ]);

        $orderPayment = OrderPayment::create([
            'order_id' => $order->id,
            'payment_id' => $payment->id
        ]);

        $items->each(function ($item, $key) use ($orderPayment) {
            OrderPaymentItem::create([
                'stored_item_id' => $item->id,
                'order_payment_id' => $orderPayment->id
            ]);
        });

//        StoredItem::whereIn('id', request()->input('items'))->update([
//            'deleted_at' => Carbon::now(),
//            'deleted_by_id' => auth()->user()->id
//        ]);

        return $payment->id;
    }

    private function getStoredItems()
    {
        return StoredItem::whereIn('id', \request()->get('items'))->get();
    }

    public function storedItems(Order $order)
    {
        return $order->storedItems()->with('info', 'info.item', 'info.owner', 'storageHistory.storage')->get();
    }

    public function unpaidStoredItems(Order $order)
    {
        return $order->storedItems()
            ->with('info', 'info.item', 'info.billingInfo', 'info.owner', 'storageHistory.storage')
            ->unpaid()->get();
    }

    public function orderPayments(Order $order)
    {
        $order->load('orderPayments.paidItems.storedItem');
        $payments = $order->orderPayments->filter(function ($payment) {
            $storedItems = $payment->paidItems->filter(function ($paidItem) {
                return $paidItem->storedItem->deleted_at == null;
            });

            return count($storedItems) != 0;
        });

        return $payments;
    }
}
