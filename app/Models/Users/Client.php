<?php

namespace App\Models\Users;

use App\Models\Order;
use App\Models\StoredItems\StoredItem;
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
        return $this->hasMany(Order::class, 'ownerId');
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
            'totalWeight' => round($totalWeight, 2),
            'totalCubage' => round($totalCubage, 2),
            'totalDiscount' => round($totalDiscount, 2),
            'totalPrice' => round($totalPrice, 2),
            'placesCount' => round($placesCount, 2)
        ];
    }

    public function getExpensesReport(string $dateFrom, ?string $dateTo)
    {
        $expensesDto = new ClientExpenseDto();
        $dateTo = $dateTo ? Carbon::createFromDate($dateTo)->addDay() : Carbon::now();

        $expensesDto->orderPayments = $this->orderPayments()->without([
            'branch',
            'preparedBy',
            'cashier',
            'payer',
            'payee',
            'payerAccountInBillCurrency',
            'payerAccountInSecondCurrency',
            'payeeAccountInBillCurrency',
            'payeeAccountInSecondCurrency',
            'paymentItem',
            'billCurrency',
            'secondPaidCurrency',
            'exchangeRate'
        ])
            ->where('updated_at', '>=', Carbon::now()->startOfYear())
            ->where('updated_at', '<', $dateTo)
            ->withCount('orderPaymentItems')
            ->get();

        $expensesDto->orders = $this->orders()
            ->where('created_at', '>=', Carbon::now()->startOfYear())
            ->where('created_at', '<', $dateTo)
            ->with('storedItemInfos')
            ->get();


        $expensesDto->orders->each(function ($order) {
            $order->storedItemsCount = $order->storedItemInfos->sum('count');
        });

        $expensesDto->prepareReport($dateFrom);
        return $expensesDto->toJson();
    }

    public function orderPayments()
    {
        return $this->outgoingPayments()->whereHas('paymentItem', function (Builder $query) {
            return $query->where('title', 'Списание с баланса');
        });
    }
}
