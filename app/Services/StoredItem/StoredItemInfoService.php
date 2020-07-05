<?php
/**
 *
 * @author Sultonazar Mamadazizov <sultonazar.mamadazizov@mail.ru> on 05.07.2020
 */

namespace App\Services\StoredItem;


use App\Data\Dto\StoredItem\StoredItemInfoDto;
use App\Data\MassWriters\StoredItem\StoredItemInfosMassWriter;
use App\Models\StoredItems\StoredItemInfo;
use Illuminate\Support\Collection;

class StoredItemInfoService
{
    public function massStore(Collection $infosData): Collection
    {
        return $infosData->map(function ($dto) {
            /** @var StoredItemInfoDto $dto */
            return new StoredItemInfo(
                array_merge($dto->toArray(), ['created_by_id' => auth()->id()])
            );
        })->pipe(function (Collection $storedItemInfos) {
            $infosMassWriter = new StoredItemInfosMassWriter($storedItemInfos->all());
            return collect($infosMassWriter->write());
        });
    }
}
