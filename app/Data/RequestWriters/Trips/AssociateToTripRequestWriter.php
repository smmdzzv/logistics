<?php


namespace App\Data\RequestWriters\Trips;


use App\Data\MassWriters\Trip\StoredItemTripHistoryWriter;
use App\Data\RequestWriters\RequestWriter;
use App\Models\StoredItems\StoredItemTripHistory;

class AssociateToTripRequestWriter extends RequestWriter
{

    public function __construct($input)
    {
        parent::__construct($input);
        $this->data->dissociate = [];
    }

    function write()
    {
        $this->filterRemovedItems();
        $this->dissociateRemovedItems();
        $this->filterExistingHistoryRecords();
        $this->prepareTripHistories();

        if (count($this->data->tripHiestories) > 0) {
            $this->writeHistories();
            return $this->saved->tripHistories;
        }

    }

    private function filterRemovedItems()
    {
        foreach ($this->input->trip->storedItems as $stored) {
            if (!in_array($stored->id, $this->input->storedItems))
                $this->data->dissociate[] = $stored->id;
        }
    }

    private function dissociateRemovedItems()
    {
        StoredItemTripHistory::where('trip_id', $this->input->trip->id)
            ->whereIn('stored_item_id', $this->data->dissociate)
            ->forceDelete();
    }

    private function filterExistingHistoryRecords()
    {
        $existingRecords = StoredItemTripHistory::where('trip_id', $this->input->trip->id)
            ->whereIn('stored_item_id', $this->input->storedItems)
            ->get();

        $this->data->storedItems = array_filter($this->input->storedItems,
            function ($stored) use ($existingRecords) {
                foreach ($existingRecords as $history) {
                    if ($history->stored_item_id === $stored)
                        return false;
                }
                return true;
            });
    }

    private function prepareTripHistories()
    {
        $this->data->tripHiestories = [];
        foreach ($this->data->storedItems as $stored) {
            $this->data->tripHiestories[] = new StoredItemTripHistory([
                'trip_id' => $this->input->trip->id,
                'stored_item_id' => $stored
            ]);
        }
    }

    private function writeHistories()
    {
        $writer = new StoredItemTripHistoryWriter($this->data->tripHiestories);
        $this->saved->tripHistories = $writer->write();
    }
}
