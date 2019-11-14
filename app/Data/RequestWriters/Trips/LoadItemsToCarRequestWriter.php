<?php


namespace App\Data\RequestWriters\Trips;


use App\Data\RequestWriters\RequestWriter;
use App\Models\StoredItems\StoredItemTripHistory;
use App\StoredItems\StorageHistory;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use stdClass;

class LoadItemsToCarRequestWriter extends RequestWriter
{

    /**
     * @return stdClass which contains saved models
     */
    function write()
    {
        $this->updateTripHistory();
        $this->getStorageHistoryRecords();
        $this->deleteStorageRecords();
        return $this->input->storedItems;
    }

    private function updateTripHistory()
    {
        $ids = $this->input->storedItems->map(function ($item) {
            return $item->id;
        });

        StoredItemTripHistory::whereHas('storedItem', function (Builder $query) use ($ids) {
            $query->whereIn('id', $ids->all());
        })->update([
            'loaded_at' => Carbon::now(),
            'loaded_by_id' => $this->input->employee->id
        ]);
    }

    private function getStorageHistoryRecords()
    {
        $this->data->storageHistories = new Collection();

        foreach ($this->input->storedItems as $storedItem) {
            $this->data->storageHistories = $this->data->storageHistories->concat($storedItem->storageHistories);
        }
    }

    private function deleteStorageRecords()
    {
        $deleted = $this->data->storageHistories->map(function ($item, $key) {
            return $item->id;
        });

        StorageHistory::whereIn('id', $deleted->all())->update([
            'deleted_at' => Carbon::now(),
            'deletedById' => $this->input->employee->id
        ]);
    }
}
