<?php


namespace App\Data\RequestWriters\Order;


use App\Data\MassWriters\Order\BillingInfosWriter;
use App\Data\MassWriters\Order\StorageHistoriesWriter;
use App\Data\MassWriters\Order\StoredItemInfosWriter;
use App\Data\MassWriters\Order\StoredItemsWriter;
use App\Data\RequestWriters\RequestWriter;
use App\Models\Branch;
use App\Models\Order;
use App\Models\StoredItems\StoredItemInfo;
use App\StoredItems\StorageHistory;
use App\User;
use Carbon\Carbon;
use stdClass;

class OrderRequestWriter extends RequestWriter
{

    private $client;
    private $branch;
    private $employee;
    private $storedItemInfos;
    private $customPrices;
    public $order;

    /**
     * OrderRequestWriter constructor.
     * @param User $client
     * @param Branch $branch
     * @param User $employee
     * @param array<StoredItemInfo> $storedItemInfos
     * @param array<double> $customPrices
     * @param Order $order
     */
    public function __construct($client, $branch, $employee, $storedItemInfos, $customPrices, $order = null)
    {
        $this->client = $client;
        $this->branch = $branch;
        $this->employee = $employee;
        $this->storedItemInfos = $storedItemInfos;
        $this->customPrices = $customPrices;
        $this->order = $order == null ? new Order() : $order;

        parent::__construct(null);
    }


    function write()
    {
        $this->updateOrderRelations();
        $this->createStoredInfos();
        $this->createStoredItems();
        $this->createStorageHistories();
        $this->createBillingInfos();
        $this->updateOrderStatistics();

        return $this->order;
    }

    /**
     * Creates and saves new Order
     */
    private function updateOrderRelations()
    {
        $this->order->owner()->associate($this->client);
        $this->order->branch()->associate($this->branch);
        $this->order->registeredBy()->associate(auth()->user());
        $this->order->push();
    }

    /**
     *Creates and saves orders related array of StoredItemInfo
     */
    private function createStoredInfos()
    {
        foreach ($this->storedItemInfos as $stored) {
            $stored->order_id = $this->order->id;
        }

        $infosWriter = new StoredItemInfosWriter($this->storedItemInfos);
        $this->saved->storedItemInfos = $infosWriter->write();
        $this->storedItemInfos = [];

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

//        preg_match_all($pattern, $this->employee->id, $eMatches);
//        $employeeMark =  substr(implode("", $eMatches[0]), 0, 2);

        preg_match_all($pattern, $this->client->id, $cMatches);
        $clientIntTrace = $cMatches[0][array_rand($cMatches[0])] . $cMatches[0][array_rand($cMatches[0])];
        $clientMark = substr($clientIntTrace, 0, 2);
//        $orderMark = substr(implode("", $cMatches[0]), 0, 2);

        while (!$isUnique) {
            $date = Carbon::now();
            preg_match_all($pattern, $date->isoFormat('x'), $dateMatches);
            $dateMark = substr(implode("", $dateMatches[0]), 9, 5);
            $code = $date->isoFormat('YY') . $dateMark . $clientMark . random_int(1000, 9999);
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
        $storageId = $this->branch->mainStorage->id;

        foreach ($this->saved->storedItems as $item) {
            $this->data->storageHistories[] = new StorageHistory([
                'stored_item_id' => $item->id,
                'storage_id' => $storageId,
                'registeredById' => $this->employee->id
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
            $price = $this->customPrices[$priceIndex];

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
        $this->order->updateStat($this->saved->billingInfos);
        $this->order->save();
    }
}
