<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    public function storedItems(){
        return $this->hasMany(StoredItem::class);
    }

    public function owner(){
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function registeredBy(){
        return $this->belongsTo(User::class, 'registeredBy');
    }
}
