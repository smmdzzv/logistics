<?php
/**
 *
 * @author Sultonazar Mamadazizov <sultonazar.mamadazizov@mail.ru> on 09.07.2020
 */

namespace App\Services\Trip;


use App\Models\StoredItems\StoredItem;
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
            ->pluck('tripHistory')
            ->pipe(function (Collection $tripHistories) use ($trip) {
                $this->tripHistoryService->massLoad($tripHistories->pluck('id'));
            });

        $this->storageHistoryService->massDelete($storedItemsIds);

        return true;
    }
}
