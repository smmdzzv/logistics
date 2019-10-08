<?php

namespace App\StoredItems;

use App\Models\BaseModel;
use App\Models\Branches\Storage;
use App\Models\StoredItems\StoredItem;
use Illuminate\Database\Eloquent\Model;

class StorageHistory extends BaseModel
{
    protected $guarded = [];

    public function storage(){
        return $this->belongsTo(Storage::class);
    }

    public function storedItem(){
        return $this->belongsTo(StoredItem::class);
    }
}
