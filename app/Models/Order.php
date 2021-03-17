<?php

namespace App\Models;

use App\Models\StoredItems\StoredItem;
use App\Models\StoredItems\StoredItemInfo;
use App\Models\Users\Client;
use App\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property double totalCubage
 * @property double totalWeight
 * @property double totalPrice
 * @property double totalDiscount
 * @property double totalCount
 * @property Collection<StoredItem> storedItems
 * @property Collection<StoredItemInfo> storedItemInfos
 * @property string status
 * @property string id
 * @property Collection orderPayments
 * @property Client owner
 * @property int totalPlaceCount
 */
class Order extends BaseModel
{
    use SoftDeletes;

    public const STATUS_COMPLETED = 'completed';

    protected $casts = [
        'totalCubage' => 'double',
        'totalWeight' => 'double',
        'totalPrice' => 'double',
        'totalDiscount' => 'double',
        'totalCount' => 'double',
    ];

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->totalCubage = 0;
        $this->totalWeight = 0;
        $this->totalPrice = 0;
        $this->totalDiscount = 0;
        $this->totalCount = 0;
    }

    public function storedItemInfos()
    {
        return $this->hasMany(StoredItemInfo::class);
    }

    public function storedItems()
    {
        return $this->hasManyThrough(StoredItem::class, StoredItemInfo::class);
    }

    public function owner()
    {
        return $this->belongsTo(Client::class, 'owner_id');
    }

    public function registeredBy()
    {
        return $this->belongsTo(User::class, 'registeredById');
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

//    public function payment()
//    {
//        return $this->belongsTo(Payment::class, 'paymentId');
//    }
//    public function orderPayments()
//    {
//        return $this->hasMany(StoredItemsSelection::class);
//    }

//    public function orderRemovedItems()
//    {
//        return $this->hasMany(OrderRemovedItem::class);
//    }

    public function updateStat(Array $billings)
    {
        $this->resetStat();

        foreach ($billings as $billing) {
            $this->totalCubage += $billing['totalCubage'];
            $this->totalWeight += $billing['totalWeight'];
            $this->totalPrice += $billing['totalPrice'];
            $this->totalDiscount += $billing['totalDiscount'];
            $this->totalCount += $billing['count'];
            $this->totalPlaceCount += $billing['totalPlaceCount'];
        }

        $this->roundStat();
    }

    public function resetStat()
    {
        $this->totalCubage = 0;
        $this->totalWeight = 0;
        $this->totalPrice = 0;
        $this->totalDiscount = 0;
        $this->totalCount = 0;
    }

    public function roundStat()
    {
        $this->totalCubage = round($this->totalCubage, 3);
        $this->totalWeight = round($this->totalWeight, 3);
        $this->totalPrice = round($this->totalPrice, 2);
        $this->totalDiscount = round($this->totalDiscount, 2);
//        $this->totalCount = round($this->totalCount, 2);
//        $this->totalPlaceCount = round($this->totalCount, 2);
    }

    public function complete()
    {
        $this->status = self::STATUS_COMPLETED;
        $this->save();
    }
}
