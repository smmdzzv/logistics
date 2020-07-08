<?php

namespace App\Services\StoredItem\Status;

use App\Data\MassWriters\Order\StorageHistoriesWriter;
use App\Models\Branch;
use App\Models\StoredItems\ItemsStatusChangeHistory;
use App\Models\StoredItems\StoredItemTripHistory;
use App\StoredItems\StorageHistory;
use Illuminate\Support\Collection;

/**
 *
 * @author Sultonazar Mamadazizov <sultonazar.mamadazizov@mail.ru> on 08.07.2020
 */
class BranchStoredItemsService
{
    public function store(Collection $storedItemsIds, Branch $branch): ItemsStatusChangeHistory
    {
        $storage = $branch->mainStorage;
        if (!$storage)
            abort(500, "Филиал не обладает складом");

        StorageHistory::whereIn('stored_item_id', $storedItemsIds)->delete();

        StoredItemTripHistory::whereIn('stored_item_id', $storedItemsIds)->delete();

        $storedItemsIds->map(function ($id) use ($storage) {
            return new StorageHistory([
                'storage_id' => $storage->id,
                'stored_item_id' => $id
            ]);
        })->pipe(function (Collection $storageHistories) {
            $writer = new StorageHistoriesWriter($storageHistories->all());
            $writer->write();
        });

        $history = ItemsStatusChangeHistory::create([
            'operation' => ItemsStatusChangeHistory::STORE_ITEMS
        ]);

        $history->storedItems()->sync($storedItemsIds);

        return $history;
    }
}
