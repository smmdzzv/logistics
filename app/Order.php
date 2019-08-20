<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    public function storedItems(){
        return $this->hasMany(StoredItem::class);
    }
}
