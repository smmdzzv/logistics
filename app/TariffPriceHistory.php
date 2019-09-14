<?php

namespace App;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Model;

/**
 * @property double lowerLimit
 * @property double mediumLimit
 * @property double upperLimit
 * @property double discountForLowerLimit
 * @property double discountForMediumLimit
 * @property double pricePerCube
 * @property double agreedPricePerKg
 * @property double pricePerExtraKg
 * @property double maxWeightPerCube
 * @property double maxCubage
 * @property double maxWeight
 * @property int tariff_id
 * @property int branch_id
 */
class TariffPriceHistory extends BaseModel
{
    protected $guarded = [];

    protected $casts = [
        'lowerLimit' => 'double',
        'mediumLimit' => 'double',
        'upperLimit' => 'double',
        'discountForLowerLimit' => 'double',
        'discountForMediumLimit' => 'double',
        'pricePerCube' => 'double',
        'agreedPricePerKg' => 'double',
        'pricePerExtraKg' => 'double',
        'maxWeightPerCube' => 'double',
        'maxCubage' => 'double',
        'maxWeight' => 'double'
    ];

    public function branch(){
        return $this->belongsTo(Branch::class);
    }

    public function billingInfos(){
        return $this->hasMany(BillingInfo::class);
    }

    public function tariff(){
        return $this->belongsTo(Tariff::class);
    }
}
