<?php
/**
 *
 * @author Sultonazar Mamadazizov <sultonazar.mamadazizov@mail.ru> on 05.07.2020
 */

namespace App\Services\StoredItem;


use App\Data\Dto\StoredItem\StoredItemInfoDto;
use App\Data\MassWriters\StoredItem\StoredItemInfosMassWriter;
use App\Models\Order;
use App\Models\StoredItems\StoredItemInfo;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class StoredItemInfoService
{
    private StoredItemService $storedItemsService;

    private BillingInfoService $billingService;

    public function __construct(StoredItemService $storedItemsService, BillingInfoService $billingService)
    {
        $this->storedItemsService = $storedItemsService;

        $this->billingService = $billingService;
    }

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

    public function deleteOrderStoredItems(Order $order): int
    {
        $this->storedItemsService->massDelete($order->storedItems->pluck('id'));

        $this->billingService->massDeleteByInfos($order->storedItemInfos->pluck('id'));

        return StoredItemInfo::whereIn('id', $order->storedItemInfos->pluck('id'))->update([
            'deleted_at' => Carbon::now(),
            'deleted_by_id' => auth()->id()
        ]);
    }
}
