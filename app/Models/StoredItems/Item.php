<?php
/**
 *
 * @author Sultonazar Mamadazizov <sultonazar.mamadazizov@mail.ru>
 */

namespace App\Models\StoredItems;

use App\Models\BaseModel;
use App\Models\Branch;
use App\Models\Customs\CustomsCode;
use App\Models\Tariff;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property string name
 * @property string unit
 * @property bool onlyCustomPrice
 * @property bool onlyAgreedPrice
 * @property bool applyDiscount
 * @property bool calculateByNormAndWeight
 */
class Item extends BaseModel
{
    use SoftDeletes;

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

//    public function tariff()
//    {
//        return $this->belongsTo(Tariff::class, 'tariffId');
//    }

//    public function branch()
//    {
//        return $this->belongsTo(Branch::class);
//    }

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
