<?php

namespace App\Observers\StoredItems;


use App\Models\StoredItems\StoredItem;
use App\Models\StoredItems\StoredItemInfo;
use App\StoredItems\StorageHistory;
use Rorecek\Ulid\Ulid;

class StoredItemInfoObserver
{
    /**
     * Handle the stored item info "created" event.
     *
     * @param StoredItemInfo $storedItemInfo
     * @return void
     */
    public function created(StoredItemInfo $storedItemInfo)
    {
        $ulidGenerator = new Ulid();

        $items = array();
        for($i = 0; $i < $storedItemInfo->count; $i++){
            $stored = new StoredItem([
                'infoId' => $storedItemInfo->id,
                'id' => $ulidGenerator->generate()
            ]);
            $items[] = $stored->attributesToArray();
        }

        StoredItem::insert($items);

        $storageHistories = array();
        $storageId = auth()->user()->branch->mainStorage->id;

        foreach ($items as $item){
            $history = new StorageHistory([
                'stored_item_id' => $item['id'],
                'storage_id' => $storageId,
                'id' => $ulidGenerator->generate()
            ]);
            $storageHistories[] = $history->attributesToArray();
        }

       StorageHistory::insert($storageHistories);
    }
//    public function created(StoredItemInfo $storedItemInfo)
//    {
//        $items = array();
//        for($i = 0; $i < $storedItemInfo->count; $i++){
//            $items[] = new StoredItem();
//        }
//
//        $storedItemInfo->storedItems()->saveMany($items);
//
//        $storageHistories = array();
//        foreach ($items as $item){
//            $history = new StorageHistory();
//            $history->storedItem()->associate($item);
//            $storageHistories[] = $history;
//        }
//
//        auth()->user()->branch->mainStorage->histories()->saveMany($storageHistories);
//    }

    /**
     * Handle the stored item info "updated" event.
     *
     * @param StoredItemInfo $storedItemInfo
     * @return void
     */
    public function updated(StoredItemInfo $storedItemInfo)
    {
        //
    }

    /**
     * Handle the stored item info "deleted" event.
     *
     * @param StoredItemInfo $storedItemInfo
     * @return void
     */
    public function deleted(StoredItemInfo $storedItemInfo)
    {
        //
    }

    /**
     * Handle the stored item info "restored" event.
     *
     * @param StoredItemInfo $storedItemInfo
     * @return void
     */
    public function restored(StoredItemInfo $storedItemInfo)
    {
        //
    }

    /**
     * Handle the stored item info "force deleted" event.
     *
     * @param StoredItemInfo $storedItemInfo
     * @return void
     */
    public function forceDeleted(StoredItemInfo $storedItemInfo)
    {
        //
    }
}
