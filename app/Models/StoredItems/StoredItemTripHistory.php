<?php

namespace App\Models\StoredItems;

use App\Models\BaseModel;
use App\Models\Trip;
use App\User;
use Illuminate\Database\Eloquent\SoftDeletes;

class StoredItemTripHistory extends BaseModel
{
    use SoftDeletes;

    public const STATUS_LISTED = 'listed';

    public const STATUS_ABANDONED = 'abandoned';

    public const STATUS_COMPLETED = 'completed';

    public function storedItem(){
        return $this->belongsTo(StoredItem::class);
    }

    public function trip(){
        return $this->belongsTo(Trip::class);
    }

    public function loadedBy(){
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
