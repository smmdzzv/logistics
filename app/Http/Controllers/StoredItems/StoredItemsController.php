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
    public function show($storedItem)
    {
        $storedItem = StoredItem::withTrashed()->find($storedItem);
        $storedItem->load('info', 'info.owner', 'info.billingInfo');
        $storageHistories = $storedItem->storageHistories()->latest()
            ->withTrashed()->with('storage', 'creator', 'destroyer')->get();
        $tripHistories = $storedItem->tripHistory()->latest()
            ->withTrashed()->with('trip', 'creator', 'destroyer', 'loadedBy')->get();
        return view('stored.show', compact('storedItem', 'storageHistories', 'tripHistories'));
    }
}
