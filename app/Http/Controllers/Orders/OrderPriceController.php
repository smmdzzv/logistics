<?php

namespace App\Http\Controllers\Orders;


use App\Data\RequestWriters\Order\UpdateOrderPriceRequestWriter;
use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\TariffPriceHistory;

class OrderPriceController extends Controller
{
    public function update(Order $order)
    {
        $writer = new UpdateOrderPriceRequestWriter($order);
        $writer->write();
        return redirect(route('orders.show', $order));
    }

    public function updateByTariffPriceHistory(Order $order, TariffPriceHistory $history)
    {

    }

}
