<?php

namespace App\Models;

use App\Models\Branches\Storage;
use App\Models\StoredItems\StoredItemInfo;
use App\Models\Till\Account;
use App\Models\Till\Payment;
use App\User;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property string name
 * @property  object|string country
 * @property  object|string director
 * @property string|null deleted_by_id
 * @property \DateTime deleted_at
 */
class Branch extends BaseModel
{
    use SoftDeletes;

    protected $guarded = [];

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function director()
    {
        return $this->belongsTo(User::class, 'director');
    }

    public function country()
    {
        return $this->belongsTo(Country::class, 'country');
    }

    public function storedItemInfos()
    {
        return $this->hasMany(StoredItemInfo::class);
    }

    //TODO check this
//    public function storedItems(){
//        return $this->hasManyThrough(StoredItem::class, StoredItemInfo::class);
//    }

    public function tariffPriceHistories()
    {
        return $this->hasMany(TariffPriceHistory::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class, 'branchId');
    }

    public function stores()
    {
        return $this->hasMany(Storage::class);
    }

    public function mainStorage()
    {
        return $this->hasOne(Storage::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class, 'branchId');
    }

    public function accounts(){
        return $this->morphMany(Account::class, 'owner');
    }
}
