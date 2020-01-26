<?php

namespace App\Models\StoredItems;

use App\Models\BaseModel;
use App\Models\BillingInfo;
use App\Models\Branch;
use App\Models\Customs\CustomsCode;
use App\Models\Order;
use App\Models\Trip;
use App\Models\Users\Client;
use App\Shops\Shop;

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
 * @property BillingInfo billingInfo
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
//        'placeCount' => 'double'
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

    public function customsCode(){
        return $this->belongsTo(CustomsCode::class);
    }

    public function shop(){
        return $this->belongsTo(Shop::class);
    }

    //TODO count prop

    /**
     * Finds and creates new BillingInfo for StoredItem
     * @param double $customPrice
     * @return BillingInfo
     */
    public function getBillingInfo($customPrice = null)
    {
//        $tariffPricing = $this->item->tariff->lastPriceHistory;

//        $billingInfo = $this->billingInfo ?? new BillingInfo();

        if(!$this->billingInfo){
            $this->billingInfo = new BillingInfo();
            $tariffPricing = $this->item->tariff->lastPriceHistory;
            $this->billingInfo->tariffPricing()->associate($tariffPricing);
            $this->billingInfo->storedItemInfo()->associate($this);
        }

//        $billingInfo->tariffPricing()->associate($tariffPricing);
//        $billingInfo->storedItemInfo()->associate($this);

        $this->billingInfo->totalWeight = $this->weight * $this->count;
        $this->billingInfo->totalCubage = $this->width * $this->height * $this->length * $this->count;
        $this->billingInfo->weightPerCube = $this->billingInfo->totalWeight / $this->billingInfo->totalCubage;
        $this->billingInfo->discountPerCube = 0;
        $this->billingInfo->count = $this->count;

        if ($this->item->onlyCustomPrice) {
            if (!$customPrice)
                self::throwBadMethodCallException('Custom Price is not provided');
            $this->billingInfo->setCustomPrice($customPrice);

        } else
            $this->billingInfo->calculatePrice($this);

        return $this->billingInfo;
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
