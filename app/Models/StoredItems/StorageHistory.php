<?php

namespace App\StoredItems;

use App\Models\BaseModel;
use App\Models\Branch;
use App\Models\Branches\Storage;
use App\Models\StoredItems\StoredItem;
use App\User;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property int deleted_at
 */
class StorageHistory extends BaseModel
{
    use SoftDeletes;

    protected $guarded = [];

    public function storage()
    {
        return $this->belongsTo(Storage::class);
    }

//    public function branch()
//    {
//        return $this->hasOneThrough(Branch::class, Storage::class);
//    }

    public function storedItem()
    {
        return $this->belongsTo(StoredItem::class);
    }

    public function registeredBy()
    {
        return $this->belongsTo(User::class, 'registeredById');
    }

    public function deletedBy(){
        return $this->belongsTo(User::class, 'deletedById');
    }
}
