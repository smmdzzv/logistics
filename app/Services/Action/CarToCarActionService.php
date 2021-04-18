<?php
/**
 * @author Sultonazar Mamadazizov <sultonazar.mamadazizov@mail.ru>
 */

namespace App\Services\Action;


use App\Data\Dto\Actions\CarToCarDto;
use App\Data\MassDelete\MassDelete;
use App\Data\MassWriters\Trip\StoredItemTripHistoryWriter;
use App\Models\StoredItems\StoredItemTripHistory;

class CarToCarActionService
{
    private CarToCarDto $dto;

    public function __construct(CarToCarDto $dto)
    {
        $this->dto = $dto;

    }

    public function transfer(){
        $this->detachFromCurrentTrip();

        $this->attachToTargetTrip();
    }

    private function detachFromCurrentTrip(){
        $records = $this->dto->storedItems->map(function ($item){
            return $item->tripHistory->id;
        });

        $deleter = new MassDelete($records->all(), StoredItemTripHistory::class, auth()->id());
        $deleter->delete();
    }

    private function attachToTargetTrip(){
        $newRecords = $this->dto->storedItems->map(function ($item){
            return new StoredItemTripHistory([
                'trip_id' => $this->dto->targetTripId,
                'stored_item_id' => $item->id,
                'registered_by_id' => auth()->id()
            ]);
        });

        $writer = new StoredItemTripHistoryWriter($newRecords->all());
        $writer->write();
    }
}
