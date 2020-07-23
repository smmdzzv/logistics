<?php
/**
 *
 * @author Sultonazar Mamadazizov <sultonazar.mamadazizov@mail.ru> on 09.07.2020
 */

namespace App\Services\Trip;


use App\Models\StoredItems\StoredItem;
use App\Models\StoredItems\StoredItemTripHistory;
use App\Models\Trip;
use App\Services\Storage\ItemsStorageHistoryService;
use App\Services\StoredItem\Trip\StoredItemTripHistoryService;
use Illuminate\Support\Collection;

class LoadTripItemsService
{
    private StoredItemTripHistoryService $tripHistoryService;

    private ItemsStorageHistoryService $storageHistoryService;

    public function __construct(StoredItemTripHistoryService $tripHistoryService, ItemsStorageHistoryService $storageHistoryService)
    {
        $this->tripHistoryService = $tripHistoryService;
        $this->storageHistoryService = $storageHistoryService;
    }

    public function load(Trip $trip, Collection $storedItemsIds): bool
    {
        StoredItem::with('tripHistory')
            ->whereIn('id', $storedItemsIds)
            ->get()
            ->pipe(function (Collection $storedItems) use ($trip) {
                $this->loadNotListedItems($storedItems, $trip);
                return $storedItems;
            })
            ->pluck('tripHistory')
            ->pipe(function (Collection $tripHistories) use ($trip) {
                $this->tripHistoryService->massLoad($tripHistories->pluck('id'));
            });

        $this->storageHistoryService->massDelete($storedItemsIds);

        return true;
    }

    public function loadNotListedItems(Collection $storedItems, Trip $trip)
    {
        return $storedItems->reject(function (StoredItem $storedItem) {
            return $storedItem->tripHistory;
        })->pipe(function (Collection $storedItems) use ($trip) {
            return $this->tripHistoryService
                ->massStore($trip->id, $storedItems->pluck('id'), StoredItemTripHistory::STATUS_LOADED);
        });
    }
}
