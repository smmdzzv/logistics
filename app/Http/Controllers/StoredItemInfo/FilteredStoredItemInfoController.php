<?php
/**
 *
 * @author Sultonazar Mamadazizov <sultonazar.mamadazizov@mail.ru> on 22.07.2020
 */

namespace App\Http\Controllers\StoredItemInfo;

use App\Data\Filters\StoredItemInfoFilter;
use App\Http\Controllers\BaseController;
use App\Models\StoredItems\StoredItemInfo;

class FilteredStoredItemInfoController extends BaseController
{
    public function index()
    {
        $query = StoredItemInfo::withTrashed()
            ->latest()
            ->select([
                'id', 'weight', 'width', 'height', 'length', 'count', 'owner_id', 'item_id', 'tariff_id', 'created_at', 'shop', 'deleted_at'
            ])
            ->with(
                'storedItems:id,stored_item_info_id',
                'storedItems.storageHistory:id,stored_item_id,storage_id',
                'storedItems.storageHistory.storage:id,name',
                'owner:id,code',
                'item:id,name',
                'tariff:id,name',
                'billingInfo:id,stored_item_info_id,pricePerItem'
            );

        $filter = new StoredItemInfoFilter(request()->all(), $query);
        return $filter->filter()->simplePaginate($this->pagination());
    }
}
