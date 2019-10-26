<?php


namespace App\Data\RequestWriters\Trips;


use App\Data\MassWriters\Order\StorageHistoriesWriter;
use App\Data\RequestWriters\RequestWriter;
use App\Models\StoredItems\StoredItemTripHistory;
use App\StoredItems\StorageHistory;
use Carbon\Carbon;
use stdClass;

class UnloadItemsFromCarRequestWriter extends RequestWriter
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
        $this->getStorage();

        $this->writeStorageHistoryRecords();

        $this->softDeleteTripHistories();

        return $this->saved;
    }

    /**
     *Gets branch main storage
     */
    private function getStorage()
    {
        $this->data->storage = $this->input->branch->mainStorage;
        if (!$this->data->storage)
            abort(500, "Branch doesn't have main storage");
    }

    /**
     *Creates and writes to DB StorageHistory record for input StoredItem array
     */
    private function writeStorageHistoryRecords()
    {
        $storageHistories = $this->input->storedItems->map(function ($item) {
            return new StorageHistory([
                'storage_id' => $this->data->storage->id,
                'stored_item_id' => $item->id,
                'registeredById' => $this->input->employee->id
            ]);
        });

        $writer = new StorageHistoriesWriter($storageHistories->all());
        $this->saved->storageHistories = $writer->write();
    }

    private function softDeleteTripHistories(){
        $tripHistories = $this->input->storedItems->map(function ($item) {
            return $item->tripHistory->id;
        });

        StoredItemTripHistory::whereIn('id', $tripHistories->all())->update([
            'deleted_at' => Carbon::now(),
            'deleted_by_id' => $this->input->employee->id
        ]);
    }
}
