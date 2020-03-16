<?php


namespace App\Models\Till;


use App\Models\Order;
use App\Models\SerializableModel;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class ClientExpenseDto extends SerializableModel
{
    public Collection $orderPayments;

    public Collection $orders;

    public Collection $reportData;

    public float $debtAtStart = 0;

    public int $placesCountAtStart = 0;

//    public function toJson()
//    {
//        return json_encode($this->toArray(), 0);
//    }

    public function toArray()
    {
        return [
            'reportData' => $this->reportData->values()->all(),
            'debtAtStart' => $this->debtAtStart,
            'placesCountAtStart' => $this->placesCountAtStart
        ];
    }

    public function prepareReport(string $dateFrom)
    {
        $dateFrom = Carbon::createFromDate($dateFrom);

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
                $this->debtAtStart += $item['amount'];
            } else {
                $this->placesCountAtStart -= $item['placesCount'];
                $this->debtAtStart -= $item['amount'];
            }
        });

        $this->debtAtStart = round($this->debtAtStart, 2);

        $this->reportData = $data->filter(function ($item) use ($dateFrom) {
            return $item['date'] >= $dateFrom;
        });
    }
}
