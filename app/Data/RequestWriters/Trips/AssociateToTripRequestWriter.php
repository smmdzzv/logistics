<?php


namespace App\Data\RequestWriters\Trips;


use App\Data\MassWriters\Order\StorageHistoriesWriter;
use App\Data\MassWriters\Trip\StoredItemTripHistoryWriter;
use App\Data\RequestWriters\RequestWriter;
use App\Models\StoredItems\StoredItemTripHistory;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;

class AssociateToTripRequestWriter extends RequestWriter
{

    function write()
    {
        $this->filterRemovedItems();

        $this->eraseRemovedItems();

        $this->filterForSoftDelete();

        $this->addStorageHistory();

        $this->dissociateRemovedItems();

        $this->filterExistingHistoryRecords();

        $this->prepareTripHistories();

        if (count($this->data->tripHiestories) > 0) {
            $this->writeHistories();
            return $this->saved->tripHistories;
        }

        return $this->saved;
    }

    private function filterRemovedItems()
    {
        $this->data->dissociate = [];
        foreach ($this->input->trip->storedItems as $stored) {
            if (!in_array($stored->id, $this->input->storedItems))
                $this->data->dissociate[] = $stored->id;
        }
    }

    /**
     *Deletes record of items, which wasn't loaded to car
     */
    private function eraseRemovedItems()
    {
        StoredItemTripHistory::where('trip_id', $this->input->trip->id)
            ->whereIn('stored_item_id', $this->data->dissociate)
            ->whereHas('storedItem', function (Builder $query) {
                $query->whereHas('storageHistory');
            })
            ->forceDelete();
    }

    /**
     *Filters histories of loaded items, which should be attached to branch and soft deleted
     */
    private function filterForSoftDelete()
    {
        $this->data->loadedStoredItemsHistories = StoredItemTripHistory::with('storedItem')
            ->where('trip_id', $this->input->trip->id)
            ->whereIn('stored_item_id', $this->data->dissociate)
            ->whereHas('storedItem', function (Builder $query) {
                $query->doesntHave('storageHistory');
            })
            ->get();
    }

    /**
     *Unloads items. Applies to items that were earlier loaded to car.
     */
    private function addStorageHistory()
    {
        $storedItems = $this->data->loadedStoredItemsHistories->map(function ($history) {
            return $history->storedItem;
        });

        $oldRecords = $storedItems->flatMap(function ($item) {
            return $item->storageHistory()->withTrashed()->get();
        });

        $newRecords = $oldRecords->map(function ($record) {
            $record->id = null;
            $record->deletedById = null;
            $record->deleted_at = null;
            $record->updated_at = null;
            return $record;
        });

        if ($newRecords->count() > 0) {
            $writer = new StorageHistoriesWriter($newRecords->all());
            $writer->write();
        }
    }

    /**
     *Soft deletes trip history of unloaded items.
     */
    public function dissociateRemovedItems()
    {
        $ids = $this->data->loadedStoredItemsHistories->map(function ($item, $key) {
            return $item->id;
        });

        StoredItemTripHistory::whereIn('id', $ids->all())->update([
            'deleted_at' => Carbon::now(),
            'deleted_by_id' => $this->input->employee->id
        ]);
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
                'stored_item_id' => $stored,
                'registered_by_id' => $this->input->employee->id
            ]);
        }
    }

    private function writeHistories()
    {
        $writer = new StoredItemTripHistoryWriter($this->data->tripHiestories);
        $this->saved->tripHistories = $writer->write();
    }
}
