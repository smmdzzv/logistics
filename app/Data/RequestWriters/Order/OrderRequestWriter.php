<?php


namespace App\Data\RequestWriters\Order;


use App\Data\MassWriters\Order\BillingInfosWriter;
use App\Data\MassWriters\Order\StorageHistoriesWriter;
use App\Data\MassWriters\Order\StoredItemInfosWriter;
use App\Data\MassWriters\Order\StoredItemsWriter;
use App\Data\RequestWriters\RequestWriter;
use App\Models\Order;
use App\StoredItems\StorageHistory;
use Carbon\Carbon;
use stdClass;

class OrderRequestWriter extends RequestWriter
{
    public function __construct($input)
    {
        parent::__construct($input);
    }

    /**
     * @return stdClass, which contains saved models
     * @throws \Exception
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
     * @throws \Exception
     */
    private function createStoredItems()
    {
        foreach ($this->saved->storedItemInfos as $info) {
            $items = $info->getStoredItems();
            foreach ($items as $item) {
                $item->code = $this->generateCode();
                $this->data->storedItems[] = $item;
            }
        }

        $storedWriter = new StoredItemsWriter($this->data->storedItems);
        $this->saved->storedItems = $storedWriter->write();
        $this->data->storedItems = [];
    }

    /**
     * Generates unique (in terms of order) codes
     * to distinguish same items visually
     * @throws \Exception
     */
    private function generateCode()
    {
        if (!isset($this->data->codes))
            $this->data->codes = [];

        $isUnique = false;
        $code = "";

        $pattern = '!\d+!';

        preg_match_all($pattern, $this->input->employee->id, $eMatches);
        $employeeMark =  substr(implode("", $eMatches[0]), 0, 2);

        preg_match_all($pattern, $this->input->client->id, $oMatches);
        $orderMark = substr(implode("", $oMatches[0]), 0, 2);

        while (!$isUnique) {
            $date = Carbon::now();
            preg_match_all($pattern, $date->isoFormat('x'), $dateMatches);
            $dateMark = substr(implode("", $dateMatches[0]),8,6);
            $code = $date->isoFormat('YY').$dateMark.$employeeMark.$orderMark. random_int(1000, 9999);
            $isUnique = !in_array($code, $this->data->codes);
        }
        $this->data->codes[] = $code;
        return $code;
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
                'registeredById' => $this->input->employee->id
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
        $priceIndex = 0;

        foreach ($this->saved->storedItemInfos as $info) {
            $price = $this->input->customPrices[$priceIndex];

            $this->data->billingInfos[] = $info->getBillingInfo($price);

            $priceIndex++;
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
