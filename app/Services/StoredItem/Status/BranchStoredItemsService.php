<?php

namespace App\Services\StoredItem\Status;

use App\Data\MassWriters\Order\StorageHistoriesWriter;
use App\Models\Branch;
use App\Models\StoredItems\StorageHistory;
use App\Models\StoredItems\StoredItem;
use App\Models\StoredItems\StoredItemTripHistory;
use App\Services\StoredItem\Trip\StoredItemTripHistoryService;
use Illuminate\Support\Collection;

/**
 *
 * @author Sultonazar Mamadazizov <sultonazar.mamadazizov@mail.ru> on 08.07.2020
 */
class BranchStoredItemsService
{
    private StoredItemTripHistoryService $storedItemTripHistoryService;

    public function __construct(StoredItemTripHistoryService $storedItemTripHistoryService)
    {
        $this->storedItemTripHistoryService = $storedItemTripHistoryService;
    }

    public function store(Collection $storedItemsIds, Branch $branch)
    {
        $storage = $branch->mainStorage;
        if (!$storage)
            abort(500, "Филиал не обладает складом");

        StorageHistory::whereIn('stored_item_id', $storedItemsIds)->delete();

        $this->storedItemTripHistoryService->massDelete($storedItemsIds, StoredItemTripHistory::STATUS_COMPLETED);

        $storedItemsIds->pipe(function (Collection $storedItemsIds) {
            StoredItem::whereIn('id', $storedItemsIds->all())->update(['status' => StoredItem::STATUS_STORED]);
            return $storedItemsIds;
        })->map(function ($id) use ($storage) {
            return new StorageHistory([
                'storage_id' => $storage->id,
                'stored_item_id' => $id
            ]);
        })->pipe(function (Collection $storageHistories) {
            $writer = new StorageHistoriesWriter($storageHistories->all());
            $writer->write();
        });

        //TODO check this thing
//        $history = ItemsStatusChangeHistory::create([
//            'operation' => ItemsStatusChangeHistory::STORE_ITEMS
//        ]);
//
//        $history->storedItems()->sync($storedItemsIds);
    }
}
