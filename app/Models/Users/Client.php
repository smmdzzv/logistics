<?php

namespace App\Models\Users;

use App\Models\Order;
use App\Models\StoredItems\StoredItem;
use Carbon\Carbon;
use mysql_xdevapi\Collection;

/**
 * @property Collection activeOrders
 */
class Client extends RoleUser
{
    public function getRoles()
    {
        return [
            'client'
        ];
    }

    public function storedItems()
    {
        return $this->hasMany(StoredItem::class, 'ownerId');
    }

    public function orders()
    {
        return $this->hasMany(Order::class, 'ownerId');
    }

    public function unpaidOrders()
    {
        return $this->orders()->where('paymentId', null);
    }

    public function activeOrders()
    {
        return $this->orders()->where('status', '!=', "completed");
    }

    public function getOrdersStatistics($dateFrom, $dateTo)
    {
        $orders = $this->orders()
            ->with('storedItemInfos')
            ->where('created_at', '>=', $dateFrom)
            ->where('created_at', '<=', Carbon::createFromDate($dateTo))
            ->get();

        $totalWeight = 0;
        $totalCubage = 0;
        $totalDiscount = 0;
        $totalPrice = 0;
        $placesCount = 0;

        foreach ($orders as $order) {

            $totalWeight += $order->totalWeight;
            $totalCubage += $order->totalCubage;
            $totalDiscount += $order->totalDiscount;
            $totalPrice += $order->totalPrice;

            foreach ($order->storedItemInfos as $info) {
                $placesCount += $info->count;
            }
        }

        return [
            'totalWeight' => $totalWeight,
            'totalCubage' => $totalCubage,
            'totalDiscount' => $totalDiscount,
            'totalPrice' => $totalPrice,
            'placesCount' => $placesCount
        ];
    }
}
