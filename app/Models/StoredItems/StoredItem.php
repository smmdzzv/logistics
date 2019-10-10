<?php

namespace App\Models\StoredItems;


use App\Models\BaseModel;
use App\Models\Trip;
use App\StoredItems\StorageHistory;

class StoredItem extends BaseModel
{
    protected $guarded = [];

    public function info(){
        return $this->belongsTo(StoredItemInfo::class, 'stored_item_info_id');
    }

    public function storageHistory(){
        return $this->hasMany(StorageHistory::class);
    }

    public function storage(){
        return $this->hasOne(StorageHistory::class)->latest();
    }

//    public function trips(){
//        return $this->hasManyThrough(Trip::class, StoredItemTripHistory::class);
//    }

    public function trips()
    {
        return $this->belongsToMany(
            Trip::class,
            'stored_item_trip_histories',
            'stored_item_id',
            'trip_id')
            ->using('App\Models\Pivots\BasePivot')
            ->withTimestamps();
    }

    public function tripHistory(){
        return $this->hasMany(StoredItemTripHistory::class);
    }

//    public function activeTrip(){
//        return $this->hasOneThrough(Trip::class, StoredItemTripHistory::class)->latest();
//    }
}
