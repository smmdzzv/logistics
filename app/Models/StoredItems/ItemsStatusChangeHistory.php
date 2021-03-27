<?php

namespace App\Models\StoredItems;

use App\Models\BaseModel;

/**
 * @deprecated find more general approach
*/
class ItemsStatusChangeHistory extends BaseModel
{
    public const LOAD_ITEMS_TO_CAR = 'load';
    public const STORE_ITEMS = 'store';
    public const DELIVER_ITEMS = 'deliver';

    public function storedItems()
    {
        return $this->belongsToMany(StoredItem::class)
            ->using('App\Models\Pivots\BasePivot');
    }
}
