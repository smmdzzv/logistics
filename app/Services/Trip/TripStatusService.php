<?php
/**
 *
 * @author Sultonazar Mamadazizov <sultonazar.mamadazizov@mail.ru> on 10.07.2020
 */

namespace App\Services\Trip;


use App\Data\Dto\Trip\CompletedTripDto;
use App\Models\StoredItems\StoredItemTripHistory;
use App\Models\Trip;
use Carbon\Carbon;

class TripStatusService
{
    public function setActive(Trip $trip)
    {
        $trip->update([
            'status' => Trip::STATUS_ACTIVE,
            'departureAt' => Carbon::now()
        ]);

        StoredItemTripHistory::whereIn('stored_item_id', $trip->storedItems->pluck('id'))
            ->where('status', StoredItemTripHistory::STATUS_LISTED)->update([
                'status' => StoredItemTripHistory::STATUS_ABANDONED,
                'deleted_by_id' => auth()->id(),
                'deleted_at' => Carbon::now()
            ]);
    }

    public function setCompleted(CompletedTripDto $dto)
    {
        $dto->trip->update([
            'status' => Trip::STATUS_COMPLETED,
            'returnedAt' => Carbon::now(),
            'mileageAfter' => $dto->mileageAfter,
            'totalFuelConsumption' => $dto->consumption
        ]);

        $dto->trip->decrement('fuelAmount', $dto->consumption);

        StoredItemTripHistory::whereIn('stored_item_id', $dto->trip->storedItems->pluck('id'))
            ->update([
                'status' => StoredItemTripHistory::STATUS_COMPLETED
            ]);

        //TODO create payment


    }



    public function cancelActiveStatus(Trip $trip){
        $trip->update([
            'status' => Trip::STATUS_SCHEDULED,
            'departureAt' => null
        ]);
    }
}
