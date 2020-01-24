<?php

namespace App\Models\StoredItems;

use App\Models\BaseModel;
use App\Models\Customs\CustomsCode;
use App\Models\Tariff;
use Illuminate\Database\Eloquent\Model;

/**
 * @property string name
 * @property string unit
 * @property bool onlyCustomPrice
 * @property bool onlyAgreedPrice
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
        'calculateByNormAndWeight' => 'boolean',
        'applyDiscount' => 'boolean'
    ];

    public function storedItems()
    {
        return $this->hasMany(StoredItem::class);
    }

    public function tariff()
    {
        return $this->belongsTo(Tariff::class, 'tariffId');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany to CustomsCodes
     */
    public function codes()
    {
        return $this->belongsToMany(CustomsCode::class)
            ->using('App\Models\Pivots\BasePivot')
            ->withTimestamps();
    }
}
