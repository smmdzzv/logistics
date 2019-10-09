<?php

namespace App\Models;

use App\Models\Branches\Storage;
use App\Models\StoredItems\StoredItem;
use App\Models\StoredItems\StoredItemInfo;
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

    public function storedItems(){
        return $this->hasManyThrough(StoredItem::class, StoredItemInfo::class);
    }

    public function tariffPriceHistories(){
        return $this->hasMany(TariffPriceHistory::class);
    }

    public function orders(){
        return $this->hasMany(Order::class, 'branchId');
    }

    public function storages(){
        return $this->hasMany(Storage::class);
    }

    public function mainStorage(){
        return $this->hasOne(Storage::class)->where('name', 'main');
    }
}
