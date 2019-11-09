<?php

namespace App\Http\Controllers\Orders;


use App\Data\RequestWriters\Order\UpdateOrderPriceRequestWriter;
use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\TariffPriceHistory;
use Illuminate\Database\Eloquent\Builder;

class OrderPriceController extends Controller
{
    public function update(Order $order)
    {
        $writer = new UpdateOrderPriceRequestWriter($order);
        $writer->write();
        return redirect(route('orders.show', $order));
    }

    public function updateByTariffPriceHistory(TariffPriceHistory $history)
    {
        $orders = Order::whereHas('storedItemInfos', function (Builder $query) use($history) {
            $query->whereHas('billingInfo', function (Builder $query) use($history) {
                $query->whereHas('tariffPricing', function (Builder $query) use($history){
                    $query->where('id', $history->id);
                });
            });
        })->get();

        $orders->each(function ($order){
            $writer = new UpdateOrderPriceRequestWriter($order);
            $writer->write();
        });

        return $orders;
    }

}
