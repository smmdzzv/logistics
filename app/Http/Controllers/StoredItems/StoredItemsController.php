<?php
/**
 *
 * @author Sultonazar Mamadazizov <sultonazar.mamadazizov@mail.ru> on 07.07.2020
 */

namespace App\Http\Controllers\StoredItems;


use App\Http\Controllers\BaseController;
use App\Models\StoredItems\StoredItem;

class StoredItemsController extends BaseController
{
    public function show($code)
    {
        return StoredItem::with('info.owner')
            ->where('code', $code)
            ->notDelivered()
            ->firstOrFail();
    }
}
