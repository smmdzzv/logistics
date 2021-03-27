<?php

namespace App\Services\StoredItem\Trip;

use App\Data\MassWriters\Trip\StoredItemTripHistoryWriter;
use App\Models\StoredItems\StoredItemTripHistory;
use App\Models\Trip;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

/**
 *
 * @author Sultonazar Mamadazizov <sultonazar.mamadazizov@mail.ru> on 08.07.2020
 */
class StoredItemTripHistoryService
{
    /**
     * @param Collection $storedItemIds
     * @param string $status
     */
    public function massDelete(Collection $storedItemIds, string $status)
    {
        StoredItemTripHistory::whereHas('storedItem', function (Builder $query) use ($storedItemIds, $status) {
            $query->whereIn('id', $storedItemIds);
        })->update([
            'status' => $status,
            'deleted_at' => Carbon::now(),
            'deleted_by_id' => auth()->user()->id
        ]);
    }

    public function massLoad(Collection $tripHistoriesIds)
    {
        StoredItemTripHistory::whereIn('id', $tripHistoriesIds)->update([
            'loaded_at' => Carbon::now(),
            'loaded_by_id' => auth()->id(),
            'status' => StoredItemTripHistory::STATUS_LOADED
        ]);
    }

    /**
     * @param string $tripId
     * @param Collection $storedItemIds
     * @param string $status
     * @return Collection of StoredItemTripHistory created for provided storedItems
     */
    public function massStore(string $tripId, Collection $storedItemIds, string $status): Collection
    {
        return $storedItemIds->map(function (string $id) use ($tripId, $status) {
            return new StoredItemTripHistory([
                'stored_item_id' => $id,
                'trip_id' => $tripId,
                'status' => $status
            ]);
        })->pipe(function (Collection $tripHistories) {
            $writer = new StoredItemTripHistoryWriter($tripHistories->all());
            return collect($writer->write());
        });
    }
}
