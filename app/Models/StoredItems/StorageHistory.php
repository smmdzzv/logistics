<?php

namespace App\StoredItems;

use App\Branches\Storage;
use App\Models\StoredItems\StoredItem;
use Illuminate\Database\Eloquent\Model;

class StorageHistory extends Model
{
    public function storage(){
        return $this->belongsTo(Storage::class);
    }

    public function storedItem(){
        return $this->belongsTo(StoredItem::class);
    }
}
