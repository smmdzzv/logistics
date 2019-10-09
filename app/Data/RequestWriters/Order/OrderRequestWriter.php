<?php


namespace App\Data\RequestWriters\Order;


use App\Data\MassWriters\Order\BillingInfosWriter;
use App\Data\MassWriters\Order\StorageHistoriesWriter;
use App\Data\MassWriters\Order\StoredItemInfosWriter;
use App\Data\MassWriters\Order\StoredItemsWriter;
use App\Data\RequestWriters\RequestWriter;
use App\Models\Order;
use App\StoredItems\StorageHistory;
use stdClass;

class OrderRequestWriter extends RequestWriter
{
    public function __construct($input)
    {
        parent::__construct($input);
    }

    /**
     * @return stdClass, which contains saved models
     */
    function write()
    {
        $this->createOrder();
        $this->createStoredInfos();
        $this->createStoredItems();
        $this->createStorageHistories();
        $this->createBillingInfos();
        $this->updateOrderStatistics();

        return $this->saved;
    }

    /**
     * Creates and saves new Order
     */
    private function createOrder()
    {
        $order = new Order();
        $order->owner()->associate($this->input->client);
        $order->branch()->associate($this->input->branch);
        $order->registeredBy()->associate(auth()->user());
        $order->push();

        $this->saved->order = $order;
    }

    /**
     *Creates and saves orders related array of StoredItemInfo
     */
    private function createStoredInfos()
    {
        foreach ($this->input->storedItemInfos as $stored) {
            $stored->order_id = $this->saved->order->id;
        }

        $infosWriter = new StoredItemInfosWriter($this->input->storedItemInfos);
        $this->saved->storedItemInfos = $infosWriter->write();
        $this->input->storedItemInfos = [];

    }

    /**
     *Creates and saves for each StoredItemInfo related array of StoredItems
     */
    private function createStoredItems()
    {
        foreach ($this->saved->storedItemInfos as $info) {
            $items = $info->getStoredItems();
            foreach ($items as $item) {
                $this->data->storedItems[] = $item;
            }
        }

        $storedWriter = new StoredItemsWriter($this->data->storedItems);
        $this->saved->storedItems = $storedWriter->write();
        $this->data->storedItems = [];
    }

    /**
     *Creates and saves for each StoredItem StorageHistory
     */
    private function createStorageHistories()
    {
        $storageId = $this->input->branch->mainStorage->id;

        foreach ($this->saved->storedItems as $item) {
            $this->data->storageHistories[] = new StorageHistory([
                'stored_item_id' => $item->id,
                'storage_id' => $storageId,
                'registeredById' => auth()->id()
            ]);
        }

        $historyWriters = new StorageHistoriesWriter($this->data->storageHistories);
        $this->saved->storageHistories = $historyWriters->write();
        $this->data->storageHistories = [];
    }


    /**
     *Creates and saves for each StoredItemInfo BillingInfo
     */
    private function createBillingInfos()
    {
        foreach ($this->saved->storedItemInfos as $info) {
            $this->data->billingInfos[] = $info->getBillingInfo();
        }

        $billingsWriter = new BillingInfosWriter($this->data->billingInfos);
        $this->saved->billingInfos = $billingsWriter->write();
        $this->data->billingInfos = [];
    }

    /**
     *Updates Order stat on total params based on saved BillingInfo array
     */
    private function updateOrderStatistics()
    {
        $this->saved->order->updateStat($this->saved->billingInfos);
        $this->saved->order->save();
    }
}
