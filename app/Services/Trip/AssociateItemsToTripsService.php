<?php
/**
 *
 * @author Sultonazar Mamadazizov <sultonazar.mamadazizov@mail.ru> on 10.07.2020
 */

namespace App\Services\Trip;


use App\Models\StoredItems\StoredItem;
use App\Models\StoredItems\StoredItemTripHistory;
use App\Models\Trip;
use App\Services\Storage\ItemsStorageHistoryService;
use App\Services\StoredItem\Trip\StoredItemTripHistoryService;
use Illuminate\Support\Collection;


class AssociateItemsToTripsService
{
    private ItemsStorageHistoryService $storageHistoryService;
    private StoredItemTripHistoryService $tripHistoryService;

    public function __construct(
        ItemsStorageHistoryService $storageHistoryService,
        StoredItemTripHistoryService $tripHistoryService
    )
    {
        $this->storageHistoryService = $storageHistoryService;
        $this->tripHistoryService = $tripHistoryService;
    }

    public function associate(Trip $trip, Collection $storedItemIds)
    {
        $trip->storedItems()
            ->with('storageHistory')
            ->whereNotIn('stored_items.id', $storedItemIds)
            ->get()
            ->pipe(function (Collection $canceledStoredItems) {
                $this->tripHistoryService
                    ->massDelete($canceledStoredItems->pluck('id'), StoredItemTripHistory::STATUS_CANCELED);
                return $canceledStoredItems;
            })
            ->pipe(function (Collection $canceledStoredItems) {
                $this->storageHistoryService->restoreStorageHistoryRecords($canceledStoredItems);
            });

        StoredItem::whereIn('id', $storedItemIds)->whereDoesntHave('tripHistory')
            ->get()
            ->pipe(function (Collection $storedItems) use ($trip) {
                $this->tripHistoryService
                    ->massStore($trip->id, $storedItems->pluck('id'), StoredItemTripHistory::STATUS_LISTED);
            });
    }
}
