<?php

namespace App\Http\Controllers\Orders;

use App\Data\RequestWriters\Order\DeliverOrderItemsRequestWriter;
use App\Models\Currency;
use App\Models\LegalEntities\LegalEntity;
use App\Models\Order;
use App\Models\Order\StoredItemsSelection;
use App\Models\Order\OrderPaymentItem;
use App\Models\StoredItems\StoredItem;
use App\Http\Controllers\Controller;
use App\Models\Till\Payment;
use App\Models\Till\PaymentItem;
use App\Models\Users\TrustedUser;
use Carbon\Carbon;
use OrderItemsDeliverRequest;

class OrderStoredItemsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('roles.deny:client,driver,worker');
    }

    public function index()
    {
        $orderPayment = request()->get('payment') ?
            StoredItemsSelection::with('order.owner', 'paidItems.storedItem')
                ->where('payment_id', request()->get('payment'))
                ->first() : null;

        return view('orders.edit-items-list', compact('orderPayment'));
    }

//    public function deliver(Order $order, Request $request)
//    {
//        $writer = new DeliverOrderItemsRequestWriter($request, $order);
//        $payment = $writer->write();
//        return $payment->id;
//    }

//    public function update(Order $order, OrderItemsDeliverRequest $request)
//    {
//
//    }

    public function storePaymentRequest(Order $order)
    {
//        $items = StoredItem::whereIn('id', \request()->input('items'))->unpaid()->get();
//
//        //Calculate bill
//        $unpaidStoredItemInfos = $items->load('info.billingInfo')
//            ->map(function ($item) {
//                return $item->info;
//            });
//
//        $paymentSum = $unpaidStoredItemInfos->sum(function ($info) {
//            return $info->billingInfo->pricePerItem;
//        });
//
//        $paymentSum -= $order->owner->accounts()->dollarAccount()->balance;
//
//        if (request('isDebtRequested')) {
//            $trusted = TrustedUser::where('user_id', $order->owner->id)->where('to', '>=', Carbon::now()->toDateString())
//                ->first();
//
//            if ($trusted) {
//                $paymentSum -= $trusted->maxDebt;
//            }
//        }
//        $paymentSum = round($paymentSum, 2);
//        if ($paymentSum <= 0) {
//            return abort(400, 'На балансе клиента достаточно денег для оплаты выбранных товаров');
//        }
//
//        $dollar = Currency::where('isoName', 'USD')->first()->id;
//        $payment = new Payment([
//            'branch_id' => auth()->user()->branch->id,
//            'cashier_id' => auth()->user()->id,
//            'status' => 'pending',
//            'prepared_by_id' => auth()->user()->id,
//            'payer_id' => $order->owner->id,
//            'payer_account_in_bill_currency_id' => $order->owner->accounts()->dollarAccount()->id,
//            'payer_account_in_second_currency_id' => null,
//            'payer_type' => 'user',
//            'payee_id' => auth()->user()->branch->id,
//            'payee_account_in_bill_currency_id' => auth()->user()->branch->accounts()->dollarAccount()->id,
//            'payee_account_in_second_currency_id' => null,
//            'payee_type' => 'branch',
//            'payment_item_id' => PaymentItem::firstOrCreate([
//                'title' => 'Пополнение баланса',
//                'description' => 'Пополнение долларового счета пользователя'
//            ])->id,
//            'billAmount' => $paymentSum,
//            'paidAmountInBillCurrency' => $paymentSum,
//            'paidAmountInSecondCurrency' => 0,
//            'bill_currency_id' => $dollar,
//            'second_paid_currency_id' => null,
//            'exchange_rate_id' => null,
//            'comment' => 'Пополнение баланса пользователя для оплаты заказа',
//        ]);
//
//        $payment->fillExtras();
//        $payment->saveOrFail();
//
//        $orderPayment = OrderPayment::create([
//            'order_id' => $order->id,
//            'payment_id' => $payment->id
//        ]);
//
//        $items->each(function ($item, $key) use ($orderPayment) {
//            OrderPaymentItem::create([
//                'stored_item_id' => $item->id,
//                'order_payment_id' => $orderPayment->id
//            ]);
//        });
//
//        return $payment->id;
    }

    public function show(Order $order)
    {
        return $order->storedItems()->with('info', 'info.item', 'info.owner', 'storageHistory.storage')->get();
    }

//    public function unpaidStoredItems(Order $order)
//    {
//        return $order->storedItems()
//            ->with('info', 'info.item', 'info.billingInfo', 'info.owner', 'storageHistory.storage')
//            ->unpaid()->get();
//    }

//    public function orderPayments(Order $order)
//    {
//        $order->load('orderPayments.paidItems.storedItem');
//        $payments = $order->orderPayments->filter(function ($payment) {
//            $storedItems = $payment->paidItems->filter(function ($paidItem) {
//                return $paidItem->storedItem->deleted_at == null;
//            });
//
//            return count($storedItems) != 0;
//        });
//
//        return $payments->values()->all();
//    }
}
