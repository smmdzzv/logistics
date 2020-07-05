<?php
/**
 *
 * @author Sultonazar Mamadazizov <sultonazar.mamadazizov@mail.ru> on 05.07.2020
 */

namespace App\Services\Storage;


use App\Data\MassWriters\Order\StorageHistoriesWriter;
use App\Models\Branches\Storage;
use App\Models\StoredItems\StoredItem;
use App\StoredItems\StorageHistory;
use Illuminate\Support\Collection;

class StorageHistoryService
{
    public function massStore(Collection $storedItems, Storage $storage): Collection
    {
        return $storedItems->map(function (StoredItem $item) use ($storage) {
            return new StorageHistory([
                'stored_item_id' => $item->id,
                'storage_id' => $storage->id
            ]);
        })->pipe(function (Collection $histories) {
            $historyWriters = new StorageHistoriesWriter($histories->all());
            return collect($historyWriters->write());
        });
    }
}
