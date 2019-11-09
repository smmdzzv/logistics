<?php

namespace App\Models\LostAndFound;

use App\Models\BaseModel;
use App\Models\StoredItems\StoredItem;

class LostItem extends BaseModel
{
    protected $fillable = ['stored_item_id', 'discount', 'placeCount'];

    public function storedItem(){
        return $this->belongsTo(StoredItem::class);
    }
}
