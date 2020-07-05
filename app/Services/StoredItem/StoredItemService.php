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
use App\User;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class StoredItemService
{
    private Collection $codes;

    public function __construct()
    {
        $this->codes = new Collection();
    }

    public function massStoreFromInfo(StoredItemInfo $info, $status = 'stored'): Collection
    {
        return collect([])->pad($info->count, null)->map(function ($i) use ($info, $status) {
            return new StoredItem([
                'stored_item_info_id' => $info->id,
                'status' => $status
            ]);
        })->each(function ($item) use ($info) {
            $item->code = $this->generateCode($info->owner);
        })->pipe(function (Collection $itemsCollection) {
            $storedWriter = new StoredItemsMassWriter($itemsCollection->all());
            return collect($storedWriter->write());
        });
    }

    /**
     * Generates unique (in terms of order) codes
     * to distinguish same items visually
     * @param User $owner
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
