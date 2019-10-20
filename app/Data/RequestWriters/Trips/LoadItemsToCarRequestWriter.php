<?php


namespace App\Data\RequestWriters\Trips;


use App\Data\RequestWriters\RequestWriter;
use App\StoredItems\StorageHistory;
use Illuminate\Database\Eloquent\Collection;
use stdClass;

class LoadItemsToCarRequestWriter extends RequestWriter
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
        $this->getHistoryRecords();
        $this->deleteRecords();
        return $this->input->storedItems;
    }

    private function getHistoryRecords()
    {
        $this->data->storageHistories = new Collection();

        foreach ($this->input->storedItems as $storedItem) {
            $this->data->storageHistories = $this->data->storageHistories->concat($storedItem->storageHistories);
        }
    }

    private function deleteRecords()
    {
        $deleted = $this->data->storageHistories->map(function ($item, $key) {
            return $item->id;
        });

        StorageHistory::whereIn('id', $deleted->toArray())->delete();
    }
}
