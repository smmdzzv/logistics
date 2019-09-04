<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * @property double weight
 * @property double height
 * @property double width
 * @property double length
 * @property int count
 * @property int item_id
 * @property int owner_id
 * @property  int branch_id
 */
class StoredItem extends Model
{
    public function item(){
        return $this->belongsTo(Item::class);
    }

    public function owner(){
        return $this->belongsTo(User::class);
    }

    public function branch(){
        return$this->belongsTo(Branch::class);
    }

    public function order(){
        return $this->belongsTo(Order::class);
    }

    public function billingInfo(){
        return $this->belongsTo(BillingInfo::class);
    }

    /**
     * @param $tariffId
     */
    public function setPrice($tariffId){
        $tariffPricing = TariffPriceHistory::findOrFail($tariffId);
        $billingInfo = new BillingInfo();
        $billingInfo->tariffPricing();
        $billingInfo->totalWeight = $this->weight * $this->count;
        $billingInfo->totalCubage = $this->width * $this->height * $this->length * $this->count;
        $billingInfo->weightPerCube = $billingInfo->totalWeight /  $billingInfo->totalCubage;
        $billingInfo->discountPerCube = 0;

        if($billingInfo->weightPerCube >= $tariffPricing->maxWeightPerCube){
            $billingInfo->pricePerItem = $tariffPricing->agreedPricePerKg * $billingInfo->totalWeight;
        }
        elseif ($tariffPricing->lowerLimit > 0 && $billingInfo->weightPerCube <= $tariffPricing->lowerLimit){
            $billingInfo->pricePerItem = $tariffPricing->pricePerCube - $tariffPricing->discountForLowerLimit;
            $billingInfo->discountPerCube = $tariffPricing->discountForLowerLimit;
        }
        elseif ($tariffPricing->mediumLimit > 0 && $billingInfo->weightPerCube <= $tariffPricing->mediumLimit){
            $billingInfo->pricePerItem = $tariffPricing->pricePerCube - $tariffPricing->discountForMediumLimit;
            $billingInfo->discountPerCube = $tariffPricing->discountForMediumLimit;
        }
        elseif($billingInfo->weightPerCube > $tariffPricing->upperLimit)
            $billingInfo->pricePerItem = $tariffPricing->pricePerCube +($billingInfo->weightPerCube - $tariffPricing->upperLimit) * $tariffPricing->pricePerExtraKg;
        else
            $billingInfo->pricePerItem = $tariffPricing->pricePerCube;

        $billingInfo->totalDiscount = $billingInfo->discountPerCube * $this->count;
        $billingInfo->totalPrice = $billingInfo->pricePerItem * $billingInfo->totalCubage;

        $tariffPricing->billingInfos()->save($billingInfo);
        $this->billingInfo_id = $billingInfo->id;

        //TODO remove
        return $billingInfo;
    }
}
