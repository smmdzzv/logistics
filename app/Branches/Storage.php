<?php

namespace App\Branches;

use App\Models\BaseModel;
use App\Models\Branch;
use App\StoredItems\StorageHistory;

class Storage extends BaseModel
{
    public function branch(){
        return $this->belongsTo(Branch::class);
    }

    public function history(){
        return $this->hasOne(StorageHistory::class);
    }
}
