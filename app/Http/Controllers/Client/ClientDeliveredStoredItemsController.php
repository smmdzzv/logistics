<?php
/**
 *
 * @author Sultonazar Mamadazizov <sultonazar.mamadazizov@mail.ru> on 06.07.2020
 */

namespace App\Http\Controllers\Client;


use App\Http\Controllers\BaseController;
use App\Models\Order;
use App\Models\Users\Client;
use App\Services\Order\OrderStoredItemsDeliveryService;

class ClientDeliveredStoredItemsController extends BaseController
{
    private OrderStoredItemsDeliveryService $service;

    public function __construct(OrderStoredItemsDeliveryService $service)
    {
        $this->service = $service;
    }

    public function store(Client $client)
    {
        $data = $this->getValidatedData();
        return $this->service->deliver($client, collect($data['storedItems']), (bool)$data['isDebtRequested'])->id;
    }

    public function getValidatedData(): array
    {
        return request()->validate([
            'storedItems' => 'required|array',
            'isDebtRequested' => 'nullable|boolean'
        ]);
    }
}
