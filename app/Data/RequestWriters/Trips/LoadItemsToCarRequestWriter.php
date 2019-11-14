<?php


namespace App\Data\RequestWriters\Trips;


use App\Data\RequestWriters\RequestWriter;
use App\StoredItems\StorageHistory;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use stdClass;

class LoadItemsToCarRequestWriter extends RequestWriter
{

    /**
     * @return stdClass which contains saved models
     */
    function write()
    {
        $this->getHistoryRecords();
        $this->deleteStorageRecords();
        return $this->input->storedItems;
    }

    private function getHistoryRecords()
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
