<?php

namespace App\Models\StoredItems;

use App\Models\BaseModel;
use App\Models\BillingInfo;
use App\Models\Branch;
use App\Models\Order;
use App\Models\Trip;
use App\Models\Users\Client;

/**
 * @property double weight
 * @property double height
 * @property double width
 * @property double length
 * @property string item_id
 * @property string branch_id
 * @property string ownerId
 * @property Item item
 * @property double count
 * @property double placeCount
 */
class StoredItemInfo extends BaseModel
{
    protected $guarded = [];

    protected $casts = [
        'weight' => 'double',
        'height' => 'double',
        'length' => 'double',
        'width' => 'double',
        'count' => 'double',
        'placeCount' => 'double'
    ];

    public function item()
    {
        return $this->belongsTo(Item::class);
    }

    public function owner()
    {
        return $this->belongsTo(Client::class, 'ownerId', 'id', 'users');
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
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
        return $this->hasMany(StoredItem::class);
    }

    //TODO count prop

    /**
     * Finds and creates new BillingInfo for StoredItem
     * @param double $customPrice
     * @return BillingInfo
     */
    public function getBillingInfo($customPrice = null)
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

        if ($this->item->onlyCustomPrice) {
            if (!$customPrice)
                self::throwBadMethodCallException('Custom Price is not provided');
            $billingInfo->setCustomPrice($customPrice);

        } else
            $billingInfo->calculatePrice($this);

        return $billingInfo;
    }


    /**
     * @return array of StoredItem
     */
    public function getStoredItems()
    {
        $items = array();
        for ($i = 0; $i < $this->count; $i++) {
            $items[] = new StoredItem(['stored_item_info_id' => $this->id]);
        }

        return $items;
    }
}
