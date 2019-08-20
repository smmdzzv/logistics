<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * @property string name
 * @property bool isPriceCustom
 * @property  int tariff_id
 * @property string unit
 */
class Item extends Model
{
    public function storedItems(){
        return $this->$this->hasMany(StoredItem::class);
    }

    public function tariff(){
        return $this->belongsTo(Tariff::class);
    }
}
