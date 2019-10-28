<?php

namespace App\Models\StoredItems;

use App\Models\BaseModel;
use App\Models\Tariff;
use Illuminate\Database\Eloquent\Model;

/**
 * @property string name
 * @property string unit
 * @property bool onlyCustomPrice
 * @property bool applyDiscount
 * @property string tariffId
 * @property Tariff tariff
 */
class Item extends BaseModel
{
    protected $guarded = [];

    protected $casts = [
        'onlyCustomPrice' => 'boolean',
        'onlyAgreedPrice' => 'boolean',
        'applyDiscount' => 'boolean'
    ];

    public function storedItems(){
        return $this->$this->hasMany(StoredItem::class);
    }

    public function tariff(){
        return $this->belongsTo(Tariff::class, 'tariffId');
    }
}
