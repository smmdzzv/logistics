<?php

namespace App\Models;

use App\Models\BaseModel;
use App\Models\StoredItems\StoredItem;
use App\Models\StoredItems\StoredItemInfo;
use App\Models\Till\Payment;
use App\Models\Users\Client;
use App\User;
use Illuminate\Database\Eloquent\Model;

/**
 * @property double totalCubage
 * @property double totalWeight
 * @property double totalPrice
 * @property double totalDiscount
 * @property double totalCount
 */
class Order extends BaseModel
{
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
    //TODO add this
//    public function storedItems()
//    {
//        return $this->hasMany(StoredItem::class);
//    }

    public function storedItemInfos(){
        return $this->hasMany(StoredItemInfo::class);
    }

    public function owner()
    {
        return $this->belongsTo(Client::class, 'ownerId');
    }

    public function registeredBy()
    {
        return $this->belongsTo(User::class, 'registeredById');
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class, 'branchId');
    }

    public function payment()
    {
        return $this->belongsTo(Payment::class, 'paymentId');
    }

    public function updateStat(Array $billings)
    {
        foreach ($billings as $billing) {
            $this->totalCubage += $billing['totalCubage'];
            $this->totalWeight += $billing['totalWeight'];
            $this->totalPrice += $billing['totalPrice'];
            $this->totalDiscount += $billing['totalDiscount'];
            $this->totalCount += $billing['count'];
        }

        $this->roundStat();
    }

    public function roundStat()
    {
        $this->totalCubage = round($this->totalCubage, 2);
        $this->totalWeight = round($this->totalWeight, 2);
        $this->totalPrice = round($this->totalPrice, 2);
        $this->totalDiscount = round($this->totalDiscount, 2);
        $this->totalCount = round($this->totalCount, 2);
    }
}
