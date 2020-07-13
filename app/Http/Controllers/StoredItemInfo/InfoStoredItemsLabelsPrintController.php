<?php
/**
 *
 * @author Sultonazar Mamadazizov <sultonazar.mamadazizov@mail.ru> on 13.07.2020
 */

namespace App\Http\Controllers\StoredItemInfo;


use App\Http\Controllers\BaseController;
use App\Models\StoredItems\StoredItem;

class InfoStoredItemsLabelsPrintController extends BaseController
{
    public function show($info)
    {
        $storedItems = StoredItem::where('stored_item_info_id', $info)->with('info.owner', 'info.item')->get();

        return view('print.order-labels', compact('storedItems'));
    }
}
