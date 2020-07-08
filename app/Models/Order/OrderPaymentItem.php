<?php

namespace App\Models\Order;

use App\Models\BaseModel;
use App\Models\StoredItems\StoredItem;
use Illuminate\Database\Eloquent\SoftDeletes;

/**@deprecated */
class OrderPaymentItem extends BaseModel
{
    use SoftDeletes;

    protected $guarded = [];

    public function storedItem()
    {
        return $this->belongsTo(StoredItem::class)->withTrashed();
    }

    public function orderPayment()
    {
        return $this->belongsTo(StoredItemsSelection::class);
    }
}
