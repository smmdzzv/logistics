<?php

namespace App\Models;

use App\Models\Branches\Storage;
use App\Models\StoredItems\StoredItem;
use App\Models\StoredItems\StoredItemInfo;
use App\Models\Till\Payment;
use App\User;

/**
 * @property string name
 * @property  object|string country
 * @property  object|string director
 */
class Branch extends BaseModel
{
    protected $guarded = [];

    public function users(){
        return $this->hasMany(User::class );
    }

    public function director(){
        return $this->belongsTo(User::class, 'director');
    }

    public function country(){
        return $this->belongsTo(Country::class, 'country');
    }

    public function storedItemInfos(){
        return $this->hasMany(StoredItemInfo::class);
    }

    //TODO check this
    public function storedItems(){
        return $this->hasManyThrough(StoredItem::class, StoredItemInfo::class);
    }

    public function tariffPriceHistories(){
        return $this->hasMany(TariffPriceHistory::class);
    }

    public function orders(){
        return $this->hasMany(Order::class, 'branchId');
    }

    public function stores(){
        return $this->hasMany(Storage::class);
    }

    public function mainStorage(){
        return $this->hasOne(Storage::class);
    }

    public function payments(){
        return $this->hasMany(Payment::class, 'branchId');
    }
}
