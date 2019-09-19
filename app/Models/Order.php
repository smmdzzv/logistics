<?php

namespace App\Models;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Model;

class Order extends BaseModel
{
    public function storedItems(){
        return $this->hasMany(StoredItem::class);
    }

    public function owner(){
        return $this->belongsTo(User::class, 'owner');
    }

    public function registeredBy(){
        return $this->belongsTo(User::class, 'registeredBy');
    }

    public function branch(){
        return $this->belongsTo(Branch::class, 'branch');
    }
}
