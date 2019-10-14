<?php

namespace App\Models\Branches;

use App\Models\BaseModel;
use App\Models\Branch;
use App\Models\StoredItems\StoredItem;
use App\StoredItems\StorageHistory;

class Storage extends BaseModel
{
    public function branch(){
        return $this->belongsTo(Branch::class);
    }

    public function histories(){
        return $this->hasMany(StorageHistory::class);
    }

    public function storedItems(){
            return $this->belongsToMany(
                StoredItem::class,
                'storage_histories',
                'storage_id',
                'stored_item_id')
                ->using('App\Models\Pivots\BasePivot')
                ->withTimestamps();
    }
}
