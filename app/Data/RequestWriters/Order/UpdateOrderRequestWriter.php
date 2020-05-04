<?php


namespace App\Data\RequestWriters\Order;


use App\Models\Order\OrderRemovedItem;
use Carbon\Carbon;

class UpdateOrderRequestWriter extends OrderRequestWriter
{
    private $existingStoredItemInfos = array();

    function write()
    {
        $this->handleDeletedStoredItemInfos();

        $this->updateOrderRelations();

        $this->filterExistingStoredItemInfos();

        $this->updateExistingStoredItemInfos();

        $this->createStoredInfos();

        $this->createStoredItems();

        $this->createStorageHistories();

        $this->storedItemInfos = array_merge($this->storedItemInfos, $this->existingStoredItemInfos);

        $this->createBillingInfos();

        $this->updateOrderStatistics();

        $this->updateOrderStatus();

        return $this->order;
    }

    private function handleDeletedStoredItemInfos()
    {
        $ids = collect($this->storedItemInfos)
            ->map(function ($item) {
                return $item->id;
            })
            ->reject(function ($id) {
                return empty($id);
            });

        $storeItemInfosToDelete = $this->order->storedItemInfos
            ->filter(function ($info) use ($ids) {
                return !$ids->contains($info->id);
            });

        $storeItemInfosToDelete->each(function ($info) {
            $info->storedItems()->update([
                'deleted_at' => Carbon::now(),
                'deleted_by_id' => $this->employee->id
            ]);

            OrderRemovedItem::create([
                'stored_item_info_id' => $info->id,
                'order_id' => $this->order->id
            ]);

            $info->update([
                'deleted_at' => Carbon::now(),
                'deleted_by_id' => $this->employee->id
            ]);
        });
    }

    private function filterExistingStoredItemInfos()
    {
        $this->storedItemInfos = array_filter($this->storedItemInfos, function ($info) {
            if ($info->id)
                $this->existingStoredItemInfos[] = $info;
            return $info->id == null;
        });
    }

    private function updateExistingStoredItemInfos()
    {
        $updatedStoredItemInfos = array();
        foreach ($this->existingStoredItemInfos as $info) {
            $storedItemInfo = $this->order->storedItemInfos->firstWhere('id', $info->id);
//                            $attr =$info->attributesToArray();
            $storedItemInfo->fill($info->attributesToArray());
            $storedItemInfo->ownerId = $this->client->id;
            $storedItemInfo->save();

            $storedItemInfo->billingInfo()->update([
                'deleted_by_id' => $this->employee->id,
                'deleted_at' => Carbon::now()
            ]);

            $updatedStoredItemInfos[] = $storedItemInfo;
//                $info->order_id = $this->order->id;
//                $attr =$info->attributesToArray();
//                unset($attr['id']);
//                $res = StoredItemInfo::create($attr);
//            dd($res);
        }

        $this->existingStoredItemInfos = $updatedStoredItemInfos;
    }

    private function updateOrderStatus()
    {
        $this->order->load('storedItemInfos');
        if ($this->order->storedItemInfos->count() === 0) {
            $this->order->delete();
        }
    }
}
