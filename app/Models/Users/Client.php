<?php

namespace App\Models\Users;

use App\Models\Order;
use App\Models\StoredItems\StoredItem;
use App\Models\StoredItems\StoredItemInfoTotalStat;
use App\Models\Till\ClientExpenseDto;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

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

    public function unpaidOrders()
    {
        return $this->orders()->where('paymentId', null);
    }

    public function orders()
    {
        return $this->hasMany(Order::class, 'owner_id');
    }

    public function activeOrders()
    {
        return $this->orders()
            ->where('status', '!=', "completed")
            ->orWhere('status', null);
    }

    public function orderPayments()
    {
        return $this->outgoingPayments()->whereHas('paymentItem', function (Builder $query) {
            return $query->where('title', 'Списание с баланса');
        });
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
            'totalWeight' => round($totalWeight, 3),
            'totalCubage' => round($totalCubage, 3),
            'totalDiscount' => round($totalDiscount, 2),
            'totalPrice' => round($totalPrice, 2),
            'placesCount' => round($placesCount, 2)
        ];
    }

    public function getStoredItemInfosStat($dateTo, $query)
    {
        $infos = $query->where('created_at', '>=', Carbon::now()->startOfYear())
            ->where('created_at', '<', $dateTo)->get();
        $stat = new StoredItemInfoTotalStat();

        foreach ($infos as $info) {
            $storedItemsCount = $info->storedItems->count();
            $stat->totalCubage += $info->width * $info->length * $info->height * $storedItemsCount;
            $stat->totalWeight += $info->weight * $storedItemsCount;
            $stat->totalPlacesCount += $info->storedItems->count();
            $stat->totalPrice += $info->billingInfo->pricePerItem * $storedItemsCount;
            $stat->weightPerCubeSum += round($info->weight / ($info->width * $info->length * $info->height), 2);
        }

        if ($infos->count() > 0)
            $stat->averageWeightPerCube = $stat->weightPerCubeSum / $infos->count();

        $stat->totalPrice = round($stat->totalPrice, 2);
        $stat->totalWeight = round($stat->totalWeight, 3);
        $stat->totalCubage = round($stat->totalCubage, 3);
        $stat->averageWeightPerCube = round($stat->averageWeightPerCube, 3);

        return $stat;
    }
}
