<?php
/**
 *
 * @author Sultonazar Mamadazizov <sultonazar.mamadazizov@mail.ru> on 05.07.2020
 */

namespace App\Services\Storage;


use App\Data\MassWriters\Order\StorageHistoriesWriter;
use App\Models\Branches\Storage;
use App\Models\StoredItems\StoredItem;
use App\Models\StoredItems\StorageHistory;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class ItemsStorageHistoryService
{
    public function massStore(Collection $storedItems, Storage $storage): Collection
    {
        return $storedItems->map(function (StoredItem $item) use ($storage) {
            $item->storageHistory()->delete();
            return new StorageHistory([
                'stored_item_id' => $item->id,
                'storage_id' => $storage->id,
                'created_by_id' => auth()->user()->id
            ]);
        })->pipe(function (Collection $histories) {
            $historyWriters = new StorageHistoriesWriter($histories->all());
            return collect($historyWriters->write());
        });
    }

    public function massDelete(Collection $storedItemIds)
    {
        StorageHistory::whereHas('storedItem', function (Builder $query) use ($storedItemIds) {
            $query->whereIn('id', $storedItemIds->all());
        })->update([
            'deleted_at' => Carbon::now(),
            'deleted_by_id' => auth()->id()
        ]);
    }

    public function restoreStorageHistoryRecords(Collection $canceledStoredItems)
    {
        $canceledStoredItems->reject(function (StoredItem $storedItem) {
            return $storedItem->storageHistory === null;
        })->flatMap(function (StoredItem $storedItem) {
            return $storedItem->storageHistory()->withTrashed()->get();
        })->map(function (StorageHistory $trashedRecord) {
            $trashedRecord->id = null;
            $trashedRecord->deleted_by_id = null;
            $trashedRecord->deleted_at = null;
            $trashedRecord->updated_at = null;
            return $trashedRecord;
        })->pipe(function (Collection $newRecords) {
            $writer = new StorageHistoriesWriter($newRecords->all());
            return $writer->write();
        });
    }
}
