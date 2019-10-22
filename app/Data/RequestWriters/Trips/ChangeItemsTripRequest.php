<?php


namespace App\Data\RequestWriters\Trips;


use App\Data\MassDelete\MassDelete;
use App\Data\MassWriters\Trip\StoredItemTripHistoryWriter;
use App\Data\RequestWriters\RequestWriter;
use App\Models\StoredItems\StoredItemTripHistory;
use stdClass;

class ChangeItemsTripRequest extends RequestWriter
{

    public function __construct($input)
    {
        parent::__construct($input);
    }

    /**
     * @return stdClass which contains saved models
     */
    function write()
    {
        $this->detachFromCurrentTrip();

        $this->attachToTargetTrip();

        return $this->saved;
    }

    private function detachFromCurrentTrip(){
        $records = $this->input->storedItems->map(function ($item){
            return $item->tripHistory->id;
        });

        $deleter = new MassDelete($records->all(), StoredItemTripHistory::class, $this->input->employee->id);
        $deleter->delete();
    }

    private function attachToTargetTrip(){
        $newRecords = $this->input->storedItems->map(function ($item){
            return new StoredItemTripHistory([
                'trip_id' => $this->input->targetTrip->id,
                'stored_item_id' => $item->id,
                'registered_by_id' => $this->input->employee->id
            ]);
        });

        $writer = new StoredItemTripHistoryWriter($newRecords->all());
        $this->saved->tripHistories = $writer->write();
    }
}
