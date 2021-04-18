<?php
/**
 * @author Sultonazar Mamadazizov <sultonazar.mamadazizov@mail.ru>
 */

namespace App\Services\Action;


use App\Data\Dto\Actions\CarToBranchDto;
use App\Models\Branches\Storage;
use App\Models\StoredItems\StoredItemTripHistory;
use App\Services\Storage\ItemsStorageHistoryService;
use Carbon\Carbon;

class CarToBranchActionService
{
    private CarToBranchDto $dto;

    private Storage $storage;

    private ItemsStorageHistoryService $itemsStorageHistoryService;

    public function __construct(CarToBranchDto $dto){
        $this->dto =$dto;

        $this->itemsStorageHistoryService = new ItemsStorageHistoryService();
    }

    public function unload(){
        $this->getStorage();

        $this->writeStorageHistoryRecords();

        $this->softDeleteTripHistories();
    }

    /**
     *Gets branch main storage
     */
    private function getStorage()
    {
        $this->storage = $this->dto->branch->mainStorage;
        if (!$this->storage)
            abort(500, "Branch doesn't have main storage");
    }

    /**
     *Creates and writes to DB StorageHistory record for input StoredItem array
     */
    private function writeStorageHistoryRecords()
    {
        $this->itemsStorageHistoryService->massStore($this->dto->storedItems, $this->storage);
//        $storageHistories = $this->dto->storedItems->map(function ($item) {
//            return new StorageHistory([
//                'storage_id' => $this->storage->id,
//                'stored_item_id' => $item->id,
//                'registeredById' => auth()->id()
//            ]);
//        });
//
//        $writer = new StorageHistoriesWriter($storageHistories->all());
//        $writer->write();
    }

    private function softDeleteTripHistories(){
        $tripHistories = $this->dto->storedItems->map(function ($item) {
            return $item->tripHistory->id;
        });

        StoredItemTripHistory::whereIn('id', $tripHistories->all())->update([
            'deleted_at' => Carbon::now(),
            'deleted_by_id' => auth()->id()
        ]);
    }
}
