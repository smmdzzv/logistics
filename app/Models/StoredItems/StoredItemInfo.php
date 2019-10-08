<?php

namespace App\Models\StoredItems;

use App\Models\BaseModel;
use App\Models\BillingInfo;
use App\Models\Branch;
use App\Models\Order;
use App\Models\Trip;
use App\Models\Users\Client;
use Illuminate\Database\Eloquent\Model;

/**
 * @property double weight
 * @property double height
 * @property double width
 * @property double length
 * @property int item_id
 * @property int branch_id
 * @property string ownerId
 * @property Item item
 * @property int count
 */
class StoredItemInfo extends BaseModel
{
    protected $guarded = [];

    protected $casts = [
        'weight' => 'double',
        'height' => 'double',
        'length' => 'double',
        'width' => 'double',
    ];

    public function item()
    {
        return $this->belongsTo(Item::class);
    }

    public function owner()
    {
        return $this->belongsTo(Client::class, 'ownerId', 'id', 'users');
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function trip()
    {
        return $this->belongsTo(Trip::class, 'tripId');
    }

    public function billingInfo()
    {
        return $this->hasOne(BillingInfo::class);
    }

    public function storedItems()
    {
        return $this->hasMany(StoredItem::class, 'infoId');
    }

    //TODO count prop

    /**
     * Finds and creates new BillingInfo for StoredItem
     * @return BillingInfo
     */
    public function getBillingInfo()
    {
        $tariffPricing = $this->item->tariff->lastPriceHistory;

        $billingInfo = $this->billingInfo ?? new BillingInfo();

        $billingInfo->tariffPricing()->associate($tariffPricing);
        $billingInfo->storedItemInfo()->associate($this);

        $billingInfo->totalWeight = $this->weight * $this->count;
        $billingInfo->totalCubage = $this->width * $this->height * $this->length * $this->count;
        $billingInfo->weightPerCube = $billingInfo->totalWeight / $billingInfo->totalCubage;
        $billingInfo->discountPerCube = 0;
        $billingInfo->count = $this->count;

        if ($this->item->onlyCustomPrice || $billingInfo->weightPerCube >= $tariffPricing->maxWeightPerCube) {
            $billingInfo->pricePerItem = $tariffPricing->agreedPricePerKg * $billingInfo->totalWeight;
        } elseif ($this->item->applyDiscount) {
            if ($tariffPricing->lowerLimit > 0 && $billingInfo->weightPerCube <= $tariffPricing->lowerLimit) {
                $billingInfo->pricePerItem = $tariffPricing->pricePerCube - $tariffPricing->discountForLowerLimit;
                $billingInfo->discountPerCube = $tariffPricing->discountForLowerLimit;
            } elseif ($tariffPricing->mediumLimit > 0 && $billingInfo->weightPerCube <= $tariffPricing->mediumLimit) {
                $billingInfo->pricePerItem = $tariffPricing->pricePerCube - $tariffPricing->discountForMediumLimit;
                $billingInfo->discountPerCube = $tariffPricing->discountForMediumLimit;
            }
        } elseif ($billingInfo->weightPerCube > $tariffPricing->upperLimit)
            $billingInfo->pricePerItem = $tariffPricing->pricePerCube + ($billingInfo->weightPerCube - $tariffPricing->upperLimit) * $tariffPricing->pricePerExtraKg;
        else
            $billingInfo->pricePerItem = $tariffPricing->pricePerCube;

        $billingInfo->totalDiscount = $billingInfo->discountPerCube * $this->count;
        $billingInfo->totalPrice = $billingInfo->pricePerItem * $billingInfo->totalCubage;

        $billingInfo->roundData();
        return $billingInfo;
    }


    /**
     * @return array of StoredItem
     */
    public function getStoredItems()
    {
        $items  = array();
        for ($i = 0; $i < $this->count; $i++) {
            $items[] = new StoredItem(['infoId' => $this->id]);
        }

        return $items;
    }
}
