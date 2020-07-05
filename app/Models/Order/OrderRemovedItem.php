<?php

namespace App\Models\Order;

use App\Models\BaseModel;
use App\Models\StoredItems\StoredItemInfo;

/**@deprecated*/
class OrderRemovedItem extends BaseModel
{
    protected $guarded = [];

    public function storedItemInfo(){
        return $this->belongsTo(StoredItemInfo::class)->withTrashed();
    }
}
