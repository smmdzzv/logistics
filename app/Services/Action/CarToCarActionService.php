<?php
/**
 * @author Sultonazar Mamadazizov <sultonazar.mamadazizov@mail.ru>
 */

namespace App\Services\Action;


use App\Data\Dto\Actions\CarToCarDto;
use App\Data\MassDelete\MassDelete;
use App\Data\MassWriters\Trip\StoredItemTripHistoryWriter;
use App\Models\StoredItems\StoredItem;
use App\Models\StoredItems\StoredItemTripHistory;
use App\Services\StoredItem\Trip\StoredItemTripHistoryService;
use Illuminate\Support\Facades\Date;

class CarToCarActionService
{
    private CarToCarDto $dto;

    private StoredItemTripHistoryService $tripHistoryService;

    public function __construct(CarToCarDto $dto)
    {
        $this->dto = $dto;

        $this->tripHistoryService = new StoredItemTripHistoryService();

    }

    public function transfer()
    {
        $this->detachFromCurrentTrip();

        $this->attachToTargetTrip();
    }

    private function detachFromCurrentTrip()
    {
        $recordsIds = $this->dto->storedItems->map(function (StoredItem $item) {
            return $item->tripHistories->pluck('id');
        })->flatten();

        $this->tripHistoryService->massDelete($recordsIds, StoredItemTripHistory::STATUS_CANCELED);

//        $deleter = new MassDelete($records->all(), StoredItemTripHistory::class, auth()->id());
//        $deleter->delete();
    }

    private function attachToTargetTrip()
    {
//        $newRecords = $this->dto->storedItems->map(function ($item) {
//            return new StoredItemTripHistory([
//                'trip_id' => $this->dto->targetTripId,
//                'stored_item_id' => $item->id,
//                'loaded_at' => Date::now(),
//                'loaded_by_id' => auth()->id(),
//                'updated_at' => Date::now(),
//                'updated_by_id' => auth()->id(),
//                'status' => StoredItemTripHistory::STATUS_LOADED
//            ]);
//        });

//        $writer = new StoredItemTripHistoryWriter($newRecords->all());
//        $writer->write();
    }
}
