<?php /** @noinspection DuplicatedCode */

/**
 *
 * @author Sultonazar Mamadazizov <sultonazar.mamadazizov@mail.ru> on 03.07.2020
 */

namespace App\Services\StoredItem;


use App\Data\MassWriters\StoredItem\StoredItemsMassWriter;
use App\Models\StoredItems\StoredItem;
use App\Models\StoredItems\StoredItemInfo;
use App\Models\Users\Client;
use App\Services\Storage\ItemsStorageHistoryService;
use App\User;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class StoredItemService
{
    /**
     * @var Collection of generated bar codes in order to check uniqueness
     */
    private Collection $codes;

    private ItemsStorageHistoryService $storageService;

    public function __construct(ItemsStorageHistoryService $storageService)
    {
        $this->codes = new Collection();
        $this->storageService = $storageService;
    }

    public function massStoreFromInfo(StoredItemInfo $info, $quantity = null, $status = 'stored'): Collection
    {
        return collect([])->pad($quantity ?? $info->count * $info->placeCount, null)->map(function ($i) use ($info, $status) {
            return new StoredItem([
                'stored_item_info_id' => $info->id,
                'status' => $status,
                'created_by_id' => auth()->id()
            ]);
        })->each(function ($item) use ($info) {
            $item->code = $this->generateCode($info->owner);
        })->pipe(function (Collection $itemsCollection) {
            $storedWriter = new StoredItemsMassWriter($itemsCollection->all());
            return collect($storedWriter->write());
        });
    }

    public function massUpdateFromInfo(StoredItemInfo $info)
    {
        $diff = $info->storedItems()->count() - $info->getTotalPlaceCount();
        if ($diff > 0) {
            $info->storedItems()
                ->with('storageHistory')
                ->limit($diff)
                ->get()
                ->each(function (StoredItem $item) {
                    $this->delete($item, StoredItem::STATUS_DELETED);
                });
        } else {
            $this->massStoreFromInfo($info, abs($diff));
        }

        return $info->storedItems;
    }

    /**
     * @param Collection $storedItems ids
     * @param string $status
     * @return int
     */
    public function massDelete(Collection $storedItems, string $status): int
    {
        $this->storageService->massDelete($storedItems);

        return StoredItem::whereIn('id', $storedItems->all())->update([
            'deleted_at' => Carbon::now(),
            'deleted_by_id' => auth()->id(),
            'status' => $status
        ]);
    }

    public function delete(StoredItem $storedItem, string $status)
    {
        $storedItem->storageHistory->delete();

        return $storedItem->update([
            'deleted_at' => Carbon::now(),
            'deleted_by_id' => auth()->id(),
            'status' => $status
        ]);
    }

    /**
     * Generates unique (in terms of order) codes
     * to distinguish same items visually
     * @param Client $owner
     * @return string
     * @throws \Exception
     */
    public function generateCode(): string
    {
        $isUnique = false;
        $code = "";

        while (!$isUnique) {
            $date = Carbon::now();
            $code = $date->isoFormat('YYMMDDHHmmss') . random_int(10000, 99999);
            $isUnique = !(boolean)StoredItem::where('code', $code)->count() && !$this->codes->contains($code);
        }

        $this->codes->push($code);

        return $code;
    }
}
