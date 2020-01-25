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
use Illuminate\Database\Eloquent\Builder;

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

    //TODO refactoring check items
    public function deliver(Order $order)
    {
        $data = new \stdClass();
        $data->order = $order;
        $data->employee = auth()->user();
        $data->storedItems = $this->getStoredItems();
        $data->isDebtRequested = request()->get('isDebtRequested');

        $writer = new DeliverOrderItemsRequestWriter($data);
        $result = $writer->write();

        return $result->payment->id;
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

        $payment = Payment::create([
            'branchId' => auth()->user()->branch->id,
            'cashierId' => auth()->user()->id,
            'currencyId' => Currency::where('isoName', 'USD')->first()->id,
            'payerId' => $order->owner->id,
            'paymentItemId' => PaymentItem::where('title', 'Оплата заказа')->first()->id,
            'accountToId' => LegalEntity::first()->accounts()->whereHas('currency', function (Builder $query) {
                $query->where('isoName', 'USD');
            })->first()->id,
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

    public function unpaidStoredItems(Order $order){
        return $order->storedItems()
            ->with('info', 'info.item', 'info.owner', 'storageHistory.storage')
            ->unpaid()->get();
    }
}
