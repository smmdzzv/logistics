<?php


namespace App\Models\Till;


use App\Models\Order;
use Illuminate\Support\Collection;

class ClientExpenseDto
{
    public Collection $orderPayments;

    public Collection $orders;

    public Collection $reportData;

    public float $balanceAtStart = 0;

    public int $placesCountAtStart = 0;

    public function toJson()
    {
        return json_encode($this->toArray(), 0);
    }

    public function toArray()
    {
        return [
            'reportData' => $this->reportData->all(),
            'balanceAtStart' => $this->balanceAtStart,
            'placesCountAtStart' => $this->placesCountAtStart
        ];
    }

    public function prepareReport(string $dateFrom)
    {
        $data = $this->orders->merge($this->orderPayments)->sortBy(function ($item, $key) {
            if ($item instanceof Order)
                return $item->created_at;
            else
                return $item->updated_at;
        })
            ->map(function ($item) {
                if ($item instanceof Order)
                    return ['type' => 'in',
                        'amount' => $item->totalPrice,
                        'discount' => $item->totalDiscount,
                        'placesCount' => $item->storedItemsCount,
                        'date' => $item->created_at];
                else
                    return ['type' => 'out',
                        'amount' => $item->billAmount,
                        'discount' => 0,
                        'placesCount' => $item->order_payment_items_count,
                        'date' => $item->updated_at];

            });

        $dataAtStart = $data->filter(function ($item) use ($dateFrom) {
            return $item['date'] < $dateFrom;
        });

        $dataAtStart->each(function ($item) {
            if ($item['type'] === 'in') {
                $this->placesCountAtStart += $item['placesCount'];
                $this->balanceAtStart += $item['amount'];
            } else {
                $this->placesCountAtStart -= $item['placesCount'];
                $this->balanceAtStart -= $item['amount'];
            }
        });

        $this->balanceAtStart = round($this->balanceAtStart, 2);

        $this->reportData = $data->filter(function ($item) use ($dateFrom) {
            return $item['date'] >= $dateFrom;
        });
    }
}
