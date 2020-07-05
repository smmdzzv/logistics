<?php
/**
 *
 * @author Sultonazar Mamadazizov <sultonazar.mamadazizov@mail.ru> on 03.07.2020
 */

namespace App\Services\Order;

use App\Data\Dto\Order\OrderDto;
use App\Data\Dto\StoredItem\StoredItemInfoDto;
use App\Models\Branch;
use App\Models\Order;
use App\Models\StoredItems\StoredItemInfo;
use App\Services\Storage\StorageHistoryService;
use App\Services\StoredItem\BillingInfoService;
use App\Services\StoredItem\StoredItemInfoService;
use App\Services\StoredItem\StoredItemService;
use Illuminate\Support\Collection;

class OrderService
{
    private StoredItemInfoService $infoService;

    private StoredItemService $itemService;

    private StorageHistoryService $storageHistoryService;

    private BillingInfoService $billingInfoService;

    public function __construct(StoredItemInfoService $infoService,
                                StoredItemService $itemService,
                                StorageHistoryService $storageHistoryService,
                                BillingInfoService $billingInfoService
    )
    {
        $this->infoService = $infoService;

        $this->itemService = $itemService;

        $this->storageHistoryService = $storageHistoryService;

        $this->billingInfoService = $billingInfoService;
    }

    public function store(OrderDto $orderDto): Order
    {
        /** @var Order $order */
        $order = Order::create($orderDto->except('id', 'storedItemInfos', 'customPrices')->toArray());

        collect($orderDto->storedItemInfos)
            ->map(function (StoredItemInfoDto $info) use ($order) {
                $info->order_id = $order->id;
                return $info;
            })->pipe(function (Collection $storedItemInfos) {
                return $this->infoService->massStore($storedItemInfos);
            })->pipe(function (Collection $storedItemInfos) use ($orderDto, $order) {
                $order->updateStat(
                    $this->billingInfoService->massStore($storedItemInfos, $orderDto->customPrices)->toArray()
                );
                $order->save();
                return $storedItemInfos;
            })->flatMap(function (StoredItemInfo $info) {
                return $this->itemService->massStoreFromInfo($info);
            })->pipe(function (Collection $storedItems) use ($orderDto) {
                return $this->storageHistoryService
                    ->massStore($storedItems, Branch::findOrFail($orderDto->branch_id)->mainStorage);
            });

        return $order;
    }

    public function update(OrderDto $orderDto)
    {

    }
}
