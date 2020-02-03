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

    protected $client;
    protected $branch;
    protected $employee;
    protected $storedItemInfos = array();
    protected $storedItems = array();
    protected $customPrices;
    protected $billingInfos = array();
    protected $storageHistories = array();
    protected $codes = array();
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
    protected function updateOrderRelations()
    {
        $this->order->owner()->associate($this->client);
        $this->order->branch()->associate($this->branch);
        $this->order->registeredBy()->associate(auth()->user());
        $this->order->push();
    }

    /**
     *Creates and saves orders related array of StoredItemInfo
     */
    protected function createStoredInfos()
    {
        foreach ($this->storedItemInfos as $stored) {
            $stored->order_id = $this->order->id;
        }

        if (count($this->storedItemInfos) > 0) {
            $infosWriter = new StoredItemInfosWriter($this->storedItemInfos);
            $this->storedItemInfos = $infosWriter->write();
        }
    }

    /**
     *Creates and saves for each StoredItemInfo related array of StoredItems
     * @throws \Exception
     */
    protected function createStoredItems()
    {
        foreach ($this->storedItemInfos as $info) {
            $items = $info->getStoredItems();
            foreach ($items as $item) {
                $item->code = $this->generateCode();
                $this->storedItems[] = $item;
            }
        }

        if (count($this->storedItems) > 0) {
            $storedWriter = new StoredItemsWriter($this->storedItems);
            $this->storedItems = $storedWriter->write();
        }
    }

    /**
     * Generates unique (in terms of order) codes
     * to distinguish same items visually
     * @throws \Exception
     */
    protected function generateCode()
    {
        if (!isset($this->codes))
            $this->codes = [];

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
            $isUnique = !in_array($code, $this->codes);
        }

        $this->codes[] = $code;
        return $code;
    }

    /**
     *Creates and saves for each StoredItem StorageHistory
     */
    protected function createStorageHistories()
    {
        $storageId = $this->branch->mainStorage->id;

        foreach ($this->storedItems as $item) {
            $this->storageHistories[] = new StorageHistory([
                'stored_item_id' => $item->id,
                'storage_id' => $storageId,
                'registeredById' => $this->employee->id
            ]);
        }

        if (count($this->storageHistories) > 0) {
            $historyWriters = new StorageHistoriesWriter($this->storageHistories);
            $this->storageHistories = $historyWriters->write();
        }
    }


    /**
     *Creates and saves for each StoredItemInfo BillingInfo
     */
    protected function createBillingInfos()
    {
        $priceIndex = 0;

        foreach ($this->storedItemInfos as $info) {
            $price = $this->customPrices[$priceIndex];

            $this->billingInfos[] = $info->getBillingInfo($price);

            $priceIndex++;
        }

        if (count($this->billingInfos) > 0) {
            $billingsWriter = new BillingInfosWriter($this->billingInfos);
            $this->billingInfos = $billingsWriter->write();
        }
    }

    /**
     *Updates Order stat on total params based on saved BillingInfo array
     */
    protected function updateOrderStatistics()
    {
        $this->order->updateStat($this->billingInfos);
        $this->order->save();
    }
}
