<?php

namespace App\Models\LostAndFound;

use App\Models\BaseModel;
use App\Models\StoredItems\StoredItem;

/**
 * @deprecated
*/
class LostStoredItem extends BaseModel
{
    protected $guarded = [];

    public function storedItem(){
        return $this->belongsTo(StoredItem::class);
    }
}
