<?php
/**
 *
 * @author Sultonazar Mamadazizov <sultonazar.mamadazizov@mail.ru> on 10.07.2020
 */

namespace App\Services\Trip;


use App\Models\StoredItems\StoredItemTripHistory;
use App\Models\Trip;
use Carbon\Carbon;

class TripStatusService
{
    public function setActive(Trip $trip)
    {
        $trip->update(['status' => Trip::STATUS_ACTIVE]);

        StoredItemTripHistory::whereIn('stored_item_id', $trip->storedItems->pluck('id'))
            ->where('status', StoredItemTripHistory::STATUS_LISTED)->update([
                'status' => StoredItemTripHistory::STATUS_ABANDONED,
                'deleted_by_id' => auth()->id(),
                'deleted_at' => Carbon::now()
            ]);
    }
}
