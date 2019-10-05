<?php

namespace App\Models;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Model;

/**
 * @property string name
 * @property string unit
 * @property bool onlyCustomPrice
 * @property bool applyDiscount
 * @property string tariffId
 */
class Item extends BaseModel
{
    protected $guarded = [];

    protected $casts = [
        'onlyCustomPrice' => 'boolean',
        'applyDiscount' => 'boolean'
    ];

    public function storedItems(){
        return $this->$this->hasMany(StoredItem::class);
    }

    public function tariff(){
        return $this->belongsTo(Tariff::class, 'tariffId');
    }
}
