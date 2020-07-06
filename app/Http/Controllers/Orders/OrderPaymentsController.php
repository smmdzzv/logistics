<?php
/**
 *
 * @author Sultonazar Mamadazizov <sultonazar.mamadazizov@mail.ru> on 06.07.2020
 */

namespace App\Http\Controllers\Orders;


use App\Models\Order;

class OrderPaymentsController
{
    public function index(Order $order)
    {
        $order->load('orderPayments.paidItems.storedItem');
        $payments = $order->orderPayments->filter(function ($payment) {
            $storedItems = $payment->paidItems->filter(function ($paidItem) {
                return $paidItem->storedItem->deleted_at == null;
            });

            return count($storedItems) != 0;
        });

        return $payments->values()->all();
    }
}
