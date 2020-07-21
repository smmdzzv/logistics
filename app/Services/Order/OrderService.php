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
use App\Models\StoredItems\StoredItem;
use App\Models\StoredItems\StoredItemInfo;
use App\Services\Storage\ItemsStorageHistoryService;
use App\Services\StoredItem\BillingInfoService;
use App\Services\StoredItem\StoredItemInfoService;
use App\Services\StoredItem\StoredItemService;
use App\StoredItems\StorageHistory;
use Illuminate\Support\Collection;

class OrderService
{
    private StoredItemInfoService $infoService;

    private StoredItemService $itemService;

    private ItemsStorageHistoryService $storageHistoryService;

    private BillingInfoService $billingInfoService;

    public function __construct(StoredItemInfoService $infoService,
                                StoredItemService $itemService,
                                ItemsStorageHistoryService $storageHistoryService,
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
            ->pipe(function (Collection $infosData) use ($order, $orderDto) {
                return $this->createStoredItemInfos($infosData, $order, $orderDto);
            });

        return $this->updateStat($order);
    }

    public function update(Order $order, OrderDto $orderDto)
    {
        collect($orderDto->storedItemInfos)
            ->map(function (StoredItemInfoDto $infoDto) use ($order) {
                $infoDto->order_id = $order->id;
                return $infoDto;
            })->pipe(function (Collection $infosData) use ($order) {
                $this->handleDeletedStoredItemInfos($order->storedItemInfos, $infosData);
                return $infosData;
            })->pipe(function (Collection $infosData) use ($order, $orderDto) {
                return $this->createOrUpdateStoredItemInfos($infosData, $order, $orderDto);
            });

        return $this->updateStat($order);
    }

    public function destroy(Order $order)
    {
        $this->infoService->deleteOrderStoredItems($order);

        return $order->delete();
    }

    public function updateStatus(Order $order)
    {
        if ($order->storedItems()->notDelivered()->count() === 0)
            $order->complete();
    }

    private function handleDeletedStoredItemInfos(Collection $existingInfos, Collection $infosDto)
    {
        $existingInfos->reject(function (StoredItemInfo $info) use ($infosDto) {
            return empty($info->id)
                || $infosDto->pluck('id')->contains($info->id);
        })->each(function (StoredItemInfo $info) {
            StorageHistory::whereIn('stored_item_id', $info->storedItems->pluck('id'))->delete();
            $info->storedItems()->delete();
            $info->delete();
        });
    }

    private function createOrUpdateStoredItemInfos(Collection $infosDto, Order $order, OrderDto $orderDto)
    {
        return $this->updateExistingStoredItemInfos($order, $infosDto, $orderDto)->merge(
            $infosDto->reject(function (StoredItemInfoDto $dto) {
                return (boolean)$dto->id;
            })->pipe(function (Collection $newInfos) use ($order, $orderDto) {
                return $this->createStoredItemInfos($newInfos, $order, $orderDto);
            })
        );
    }

    private function updateExistingStoredItemInfos(Order $order, Collection $infosDto, OrderDto $orderDto): Collection
    {
        return $order->storedItemInfos->reject(function (StoredItemInfo $info) {
            return (boolean)$info->deleted_at;
        })->each(function (StoredItemInfo $info) use ($infosDto) {
            $info->fill(
                $infosDto->firstWhere('id', $info->id)->all()
            )->save();

            $info->billingInfo()->delete();
        })->pipe(function (Collection $storedItemInfos) use ($orderDto, $order) {
            $this->createBillingInfos($order, $storedItemInfos, $orderDto);
            return $storedItemInfos;
        })->flatMap(function (StoredItemInfo $info) {
            return $this->itemService->massUpdateFromInfo($info);
        })->each(function (StoredItem $storedItem) {
            $storedItem->storageHistory()->delete();
        })->pipe(function (Collection $storedItems) use ($orderDto) {
            return $this->storageHistoryService
                ->massStore($storedItems, Branch::findOrFail($orderDto->branch_id)->mainStorage);
        });
    }

    private function createBillingInfos(Order $order, Collection $storedItemInfos, OrderDto $dto): Collection
    {
        return $this->billingInfoService->massStore($storedItemInfos, $dto->customPrices);
    }

    private function createStoredItemInfos(Collection $data, Order $order, OrderDto $orderDto): Collection
    {
        return $data->map(function (StoredItemInfoDto $info) use ($order) {
            $info->order_id = $order->id;
            return $info;
        })->pipe(function (Collection $storedItemInfos) {
            return $this->infoService->massStore($storedItemInfos);
        })->pipe(function (Collection $storedItemInfos) use ($orderDto, $order) {
            $this->createStoredItemsRelations($storedItemInfos, $orderDto, $order);
            return $storedItemInfos;
        });
    }

    private function createStoredItemsRelations(Collection $storedItemInfos, OrderDto $orderDto, Order $order)
    {
        $storedItemInfos->pipe(function (Collection $storedItemInfos) use ($orderDto, $order) {
            $this->createBillingInfos($order, $storedItemInfos, $orderDto);
            return $storedItemInfos;
        })->flatMap(function (StoredItemInfo $info) {
            return $this->itemService->massStoreFromInfo($info);
        })->pipe(function (Collection $storedItems) use ($orderDto) {
            return $this->storageHistoryService
                ->massStore($storedItems, Branch::findOrFail($orderDto->branch_id)->mainStorage);
        });
    }

    private function updateStat(Order $order): Order
    {
        $order->updateStat(
            $order->storedItemInfos()->with('billingInfo')->get()->pluck('billingInfo')->toArray()
        );

        $order->save();

        return $order;
    }
}
