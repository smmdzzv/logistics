<?php

namespace App\Models\StoredItems;

use App\Models\BaseModel;
use App\Models\Trip;
use App\User;
use Illuminate\Database\Eloquent\SoftDeletes;

class StoredItemTripHistory extends BaseModel
{
    use SoftDeletes;
    //yellow
    public const STATUS_LISTED = 'listed';
    //red
    public const STATUS_ABANDONED = 'abandoned';
    //primary
    public const STATUS_LOADED = 'loaded';
    //green
    public const STATUS_COMPLETED = 'completed';
    //gray
    public const STATUS_CANCELED = 'canceled';


    public function storedItem()
    {
        return $this->belongsTo(StoredItem::class);
    }

    public function trip()
    {
        return $this->belongsTo(Trip::class);
    }

    public function loadedBy()
    {
        return $this->belongsTo(User::class);
    }

//    public function registeredBy(){
//        return $this->belongsTo(User::class);
//    }
//
//    public function deletedBy(){
//        return $this->belongsTo(User::class);
//    }
}
