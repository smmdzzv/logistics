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
 * @property string tariff_price_history_id
 * @property double count
 */
class BillingInfo extends BaseModel
{
    public function tariffPricing(){
        return $this->belongsTo(TariffPriceHistory::class, 'tariff_price_history_id');
    }

    public function storedItem(){
        return $this->belongsTo(StoredItem::class);
    }

    public function roundData(){
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
