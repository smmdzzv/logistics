<?php

namespace App\Models\StoredItems;


use App\Models\BaseModel;
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
}