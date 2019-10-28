<?php

namespace App\Models;

use App\Models\StoredItems\StoredItemInfo;

/**
 * @property double totalWeight
 * @property double totalCubage
 * @property double weightPerCube
 * @property double pricePerItem
 * @property double discountPerCube
 * @property double totalDiscount
 * @property double totalPrice
 * @property string tariff_price_history_id
 * @property double count
 * @property TariffPriceHistory tariffPricing
 */
class BillingInfo extends BaseModel
{
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
    public function setCustomPrice($customPrice){
        $this->totalPrice = $customPrice;
        $this->pricePerItem = $customPrice / $this->count;
        $this->totalDiscount = 0;
    }

    public function calculatePrice(StoredItemInfo $storedItemInfo)
    {

        if ($storedItemInfo->item->onlyAgreedPrice || $this->weightPerCube >= $this->tariffPricing->maxWeightPerCube) {

            $this->pricePerItem = $$this->tariffPricing->agreedPricePerKg * $this->totalWeight;

        } elseif ($storedItemInfo->item->applyDiscount) {

            if ($this->tariffPricing->lowerLimit > 0 && $this->weightPerCube <= $this->tariffPricing->lowerLimit) {

                $this->pricePerItem = $this->tariffPricing->pricePerCube - $this->tariffPricing->discountForLowerLimit;
                $this->discountPerCube = $this->tariffPricing->discountForLowerLimit;

            } elseif ($this->tariffPricing->mediumLimit > 0 && $this->weightPerCube <= $this->tariffPricing->mediumLimit) {

                $this->pricePerItem = $this->tariffPricing->pricePerCube - $this->tariffPricing->discountForMediumLimit;
                $this->discountPerCube = $this->tariffPricing->discountForMediumLimit;

            }

        } elseif ($this->weightPerCube > $this->tariffPricing->upperLimit)
            $this->pricePerItem = $this->tariffPricing->pricePerCube
                + ($this->weightPerCube - $this->tariffPricing->upperLimit)
                * $this->tariffPricing->pricePerExtraKg;
        else
            $this->pricePerItem = $this->tariffPricing->pricePerCube;

        $this->totalDiscount = $this->discountPerCube * $storedItemInfo->count;
        $this->totalPrice = $this->pricePerItem * $this->totalCubage;

        $this->roundData();
    }

    public function roundData()
    {
        $this->totalWeight = round($this->totalWeight, 2);
        $this->totalCubage = round($this->totalCubage, 2);
        $this->weightPerCube = round($this->weightPerCube, 2);
        $this->pricePerItem = round($this->pricePerItem, 2);
        $this->discountPerCube = round($this->discountPerCube, 2);
        $this->totalDiscount = round($this->totalDiscount, 2);
        $this->totalPrice = round($this->totalPrice, 2);
        $this->count = round($this->count, 2);
    }
}
