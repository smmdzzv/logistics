<?php
/**
 *
 * @author Sultonazar Mamadazizov <sultonazar.mamadazizov@mail.ru> on 11.07.2020
 */

namespace App\Http\Controllers\StoredItems;


use App\Http\Controllers\BaseController;
use App\Models\StoredItems\StoredItem;

class AvailableStoredItemsController extends BaseController
{
    public function show($storedItem)
    {
        return StoredItem::with('info.owner')->where('code', $storedItem)->firstOrFail();
    }
}
