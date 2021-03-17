<?php

namespace App\Models;

use App\Models\StoredItems\StoredItemInfo;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property double totalWeight
 * @property double totalCubage
 * @property double weightPerCube
 * @property double pricePerItem
 * @property double discountPerCube
 * @property double totalDiscount
 * @property double totalPrice
 * @property string tariff_price_history_id
 * @property integer count
 * @property integer totalPlaceCount
 * @property TariffPriceHistory tariffPricing
 * @property string created_by_id
 */
class BillingInfo extends BaseModel
{
    use SoftDeletes;

    protected $casts = [
        'totalWeight' => 'double',
        'totalCubage' => 'double',
        'weightPerCube' => 'double',
        'pricePerItem' => 'double',
        'discountPerCube' => 'double',
        'totalDiscount' => 'double',
        'totalPrice' => 'double',
        'count' => 'double',
    ];

    public function tariffPricing()
    {
        return $this->belongsTo(TariffPriceHistory::class, 'tariff_price_history_id');
    }

    public function storedItemInfo()
    {
        return $this->belongsTo(StoredItemInfo::class);
    }

    /**
     * Sets price for items with onlyCustomPrice flag
     * @param $customPrice
     */
    public function setCustomPrice($customPrice)
    {
        $this->totalPrice = $customPrice;
        $this->pricePerItem = $customPrice / $this->count;
        $this->totalDiscount = 0;
    }

    public function calculatePrice(StoredItemInfo $storedItemInfo)
    {
        if ($storedItemInfo->item->onlyAgreedPrice
            || $this->weightPerCube >= $this->tariffPricing->maxWeightPerCube
            && $storedItemInfo->item->calculateByNormAndWeight) {

            $this->totalPrice = $this->tariffPricing->agreedPricePerKg * $this->totalWeight;
            $this->pricePerItem = $this->totalPrice / $this->count;

            return $this->roundData();

        }

        $pricePerCube = $this->tariffPricing->pricePerCube;

        if ($storedItemInfo->item->applyDiscount) {

            if ($this->tariffPricing->lowerLimit > 0 && $this->weightPerCube <= $this->tariffPricing->lowerLimit) {

                $pricePerCube -= $this->tariffPricing->discountForLowerLimit;
                $this->discountPerCube = $this->tariffPricing->discountForLowerLimit;

            } elseif ($this->tariffPricing->mediumLimit > 0 && $this->weightPerCube <= $this->tariffPricing->mediumLimit) {

                $pricePerCube -= $this->tariffPricing->discountForMediumLimit;
                $this->discountPerCube = $this->tariffPricing->discountForMediumLimit;
            }

        }

        if ($this->weightPerCube > $this->tariffPricing->upperLimit)
            $pricePerCube = $pricePerCube
                + ($this->weightPerCube - $this->tariffPricing->upperLimit)
                * $this->tariffPricing->pricePerExtraKg;

        $this->totalDiscount = $this->discountPerCube * $storedItemInfo->count;
        $this->totalPrice = $pricePerCube * $this->totalCubage;
        $this->pricePerItem = $this->totalPrice / $this->count;

        return $this->roundData();
    }

    public function roundData()
    {
        $this->totalWeight = round($this->totalWeight, 3);
        $this->totalCubage = round($this->totalCubage, 3);
        $this->weightPerCube = round($this->weightPerCube, 3);
        $this->pricePerItem = round($this->pricePerItem, 2);
        $this->discountPerCube = round($this->discountPerCube, 2);
        $this->totalDiscount = round($this->totalDiscount, 2);
        $this->totalPrice = round($this->totalPrice, 2);
        $this->count = round($this->count, 2);
    }
}
