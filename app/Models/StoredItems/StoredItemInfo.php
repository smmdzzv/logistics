<?php

namespace App\Models\StoredItems;

use App\Models\BaseModel;
use App\Models\BillingInfo;
use App\Models\Branch;
use App\Models\Customs\CustomsCode;
use App\Models\Customs\CustomsCodeTax;
use App\Models\Order;
use App\Models\Tariff;
use App\Models\TariffPriceHistory;
use App\Models\Users\Client;
use App\User;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Collection;

/**
 * @property double weight
 * @property double height
 * @property double width
 * @property double length
 * @property string item_id
 * @property string branch_id
 * @property string owner_id
 * @property Item item
 * @property int count
 * @property int placeCount
 * @property BillingInfo billingInfo
 * @property Tariff tariff
 * @property string id
 * @property Client owner
 * @property Collection storedItems
 * @property string deleted_at
 */
class StoredItemInfo extends BaseModel
{
    use SoftDeletes;

    protected $guarded = [];

    protected $casts = [
        'weight' => 'double',
        'height' => 'double',
        'length' => 'double',
        'width' => 'double',
        'count' => 'double'
    ];

    public function item()
    {
        return $this->belongsTo(Item::class);
    }

    public function owner()
    {
        return $this->belongsTo(Client::class, 'owner_id', 'id', 'users');
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function deletedBy(){
        return $this->belongsTo(User::class);
    }

//    public function trip()
//    {
//        return $this->belongsTo(Trip::class, 'tripId');
//    }

    public function billingInfo()
    {
        return $this->hasOne(BillingInfo::class);
    }

    public function tariff(){
        return $this->belongsTo(Tariff::class);
    }

    public function storedItems()
    {
        return $this->hasMany(StoredItem::class);
    }

    public function customsCode(){
        return $this->belongsTo(CustomsCode::class);
    }

    public function customsCodeTax(){
        return $this->belongsTo(CustomsCodeTax::class);
    }

//    public function shop(){
//        return $this->belongsTo(Shop::class);
//    }

    //TODO count prop

    /**
     * Finds and creates new BillingInfo for StoredItem
     * @param double $customPrice
     * @return BillingInfo
     */
    public function getBillingInfo($customPrice = null)
    {
        if(!$this->billingInfo){
            $this->billingInfo = new BillingInfo();
            $tariffPricing = $this->tariff->lastPriceHistory;
            $this->billingInfo->tariffPricing()->associate($tariffPricing);
            $this->billingInfo->storedItemInfo()->associate($this);
        }

        $this->billingInfo->totalWeight = $this->weight * $this->count;
        $this->billingInfo->totalCubage = $this->width * $this->height * $this->length * $this->count;
        $this->billingInfo->weightPerCube = $this->billingInfo->totalWeight / $this->billingInfo->totalCubage;
        $this->billingInfo->discountPerCube = 0;
        $this->billingInfo->count = $this->count;
        $this->billingInfo->totalPlaceCount = $this->getTotalPlaceCount();

        if ($this->item->onlyCustomPrice) {
            if (!$customPrice)
                self::throwBadMethodCallException('Custom Price is not provided');
            $this->billingInfo->setCustomPrice($customPrice);

        } else
            $this->billingInfo->calculatePrice($this);

        return $this->billingInfo;
    }


    /**
     * @deprecated
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

    public function getTotalPlaceCount(){
        return $this->count * $this->placeCount;
    }
}
