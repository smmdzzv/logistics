<?php
/**
 *
 * @author Sultonazar Mamadazizov <sultonazar.mamadazizov@mail.ru> on 06.07.2020
 */

namespace App\Http\Controllers\Orders;


use App\Http\Controllers\BaseController;
use App\Models\Order;
use App\Services\Order\OrderStoredItemsDeliveryService;

class OrderDeliveredStoredItemsController extends BaseController
{
    private OrderStoredItemsDeliveryService $service;

    public function __construct(OrderStoredItemsDeliveryService $service)
    {
        $this->service = $service;
    }

    public function store(Order $order)
    {
        $data = $this->getValidatedData();
        return $this->service->deliver($order, collect($data['storedItems']), (bool)$data['isDebtRequested']);
    }

    public function getValidatedData(): array
    {
        return request()->validate([
            'storedItems' => 'required|array',
            'isDebtRequested' => 'nullable|numeric'
        ]);
    }
}
