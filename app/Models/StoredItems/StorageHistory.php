<?php

namespace App\StoredItems;

use App\Models\BaseModel;
use App\Models\Branch;
use App\Models\Branches\Storage;
use App\Models\StoredItems\StoredItem;
use App\Models\Users\Employee;
use Illuminate\Database\Eloquent\Model;

class StorageHistory extends BaseModel
{
    protected $guarded = [];

    public function storage(){
        return $this->belongsTo(Storage::class);
    }

    public function branch(){
        return $this->hasOneThrough(Branch::class, Storage::class);
    }

    public function storedItem(){
        return $this->belongsTo(StoredItem::class);
    }

    public function registeredBy(){
        return $this->belongsTo(Employee::class, 'registeredById');
    }
}
