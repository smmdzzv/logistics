<?php

namespace App\Models;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Model;

/**
 * @property double totalWeight
 * @property double totalCubage
 * @property double weightPerCube
 * @property double pricePerItem
 * @property double discountPerCube
 * @property double totalDiscount
 * @property double totalPrice
 */
class BillingInfo extends BaseModel
{
    public function tariffPricing(){
        return $this->belongsTo(TariffPriceHistory::class);
    }

    public function storedItem(){
        return $this->belongsTo(StoredItem::class);
    }
}