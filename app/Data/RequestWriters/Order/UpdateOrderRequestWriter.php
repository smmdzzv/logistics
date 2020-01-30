<?php


namespace App\Data\RequestWriters\Order;


use App\Models\Order\OrderRemovedItem;
use Carbon\Carbon;

class UpdateOrderRequestWriter extends OrderRequestWriter
{
    function write()
    {
        $this->handleDeletedStoredItemInfos();

        parent::updateOrderRelations();
        $this->createStoredInfos();
        $this->createStoredItems();
        $this->createStorageHistories();
        parent::createBillingInfos();
        parent::updateOrderStatistics();

        return $this->order;
    }

    private function handleDeletedStoredItemInfos()
    {
        $storeItemInfosToDelete = $this->order->storedItemInfos
            ->filter(function ($value) {
                return !collect($this->storedItemInfos)->contains('id', $value);
            });

        $storeItemInfosToDelete->each(function($info){
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
}