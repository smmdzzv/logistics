<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int lowerLimit
 * @property int mediumLimit
 * @property int upperLimit
 * @property double discountForLowerLimit
 * @property double discountForMediumLimit
 * @property double pricePerCube
 * @property double agreedPricePerKg
 * @property double pricePerExtraKg
 * @property int maxWeightPerCube
 * @property int maxCubage
 * @property int maxWeight
 * @property int tariff_id
 * @property int branch_id
 */
class TariffPriceHistory extends Model
{
    public function branch(){
        return $this->belongsTo(Branch::class);
    }

    public function billingInfos(){
        return $this->hasMany(BillingInfo::class);
    }
}
