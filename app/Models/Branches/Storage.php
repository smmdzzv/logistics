<?php

namespace App\Models\Branches;

use App\Models\BaseModel;
use App\Models\Branch;
use App\StoredItems\StorageHistory;

class Storage extends BaseModel
{
    public function branch(){
        return $this->belongsTo(Branch::class);
    }

    public function histories(){
        return $this->hasMany(StorageHistory::class);
    }
}
