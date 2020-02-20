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

        $dollar = Currency::where('isoName', 'USD')->first()->id;
        $payment = Payment::create([
            'branch_id' => auth()->user()->branch->id,
            'cashier_id' => auth()->user()->id,
            'status' => 'pending',
            'prepared_by_id' => auth()->user()->id,
            'payer_id' => $order->owner->id,
            'payer_account_id' => $order->owner->accounts()->dollarAccount()->id,
            'payer_type' => 'user',
            'payee_id' => auth()->user()->branch->id,
            'payee_account_id' => auth()->user()->branch->accounts()->dollarAccount()->id,
            'payee_type' => 'branch',
            'payment_item_id' => PaymentItem::firstOrCreate([
                'title' => 'Списание с баланса',
                'description' => 'Списание денег с баланса клиента в счет оплаты заказы'
            ])->id,
            'billAmount' => $paymentSum,
            'paidAmount' => $paymentSum,
            'bill_currency_id' => $dollar,
            'paid_currency_id' => $dollar,
            'exchange_rate_id' => null,
            'comment' => 'Списание денег с баланса в счет оплаты заказа',
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

        return $payment->id;
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
