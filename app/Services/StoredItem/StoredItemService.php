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
                    $item->storageHistory->delete();
                    $item->delete();
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

        return StoredItem::whereIn('id', $storedItems)->update([
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
    protected function generateCode(Client $owner)
    {
        $isUnique = false;
        $code = "";
        $pattern = '!\d+!';

        preg_match_all($pattern, $owner->code, $cMatches);
        $ownerTrace = $cMatches[0][array_rand($cMatches[0])] . $cMatches[0][array_rand($cMatches[0])];
        $ownerMark = substr($ownerTrace, 0, 2);

        while (!$isUnique) {
            $date = Carbon::now();
            preg_match_all($pattern, $date->isoFormat('x'), $dateMatches);
            $dateString = implode("", $dateMatches[0]);
            $dateMark = substr($dateString, strlen($dateString) - 7, 6);
            $code = $date->isoFormat('YY') . $dateMark . $ownerMark . random_int(10000, 99999);
            $isUnique = !(boolean)StoredItem::where('code', $code)->count() && !$this->codes->contains($code);
        }

        $this->codes->push($code);

        return $code;
    }
}
