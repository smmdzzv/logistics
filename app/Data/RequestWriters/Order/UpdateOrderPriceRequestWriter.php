<?php


namespace App\Data\RequestWriters\Order;


use App\Data\RequestWriters\RequestWriter;
use App\Models\Order;
use App\Models\Till\Payment;

class UpdateOrderPriceRequestWriter extends RequestWriter
{
    /**
     * @var Order
     */
    private $order;

    public function __construct($order)
    {
        parent::__construct(null);

        $this->order = $order;
    }

    function write()
    {
        $billingInfos = [];

        foreach ($this->order->storedItemInfos as $info){
            $billingInfo = $info->getBillingInfo();
            $billingInfo->save();
            $billingInfos[] = $billingInfo;
        }

        $this->order->resetStat();
        $this->order->updateStat($billingInfos);
        $this->order->save();

        return $this->order;
    }
}
