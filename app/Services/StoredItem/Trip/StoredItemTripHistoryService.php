<?php

namespace App\Services\StoredItem\Trip;

use App\Models\StoredItems\StoredItemTripHistory;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

/**
 *
 * @author Sultonazar Mamadazizov <sultonazar.mamadazizov@mail.ru> on 08.07.2020
 */
class StoredItemTripHistoryService
{
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

    public function massLoad(Collection $tripHistoriesIds){
        StoredItemTripHistory::whereIn('id', $tripHistoriesIds)->update([
            'loaded_at' => Carbon::now(),
            'loaded_by_id' => auth()->id(),
            'status' => StoredItemTripHistory::STATUS_LOADED
        ]);
    }
}
