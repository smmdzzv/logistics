<?php

namespace App\Http\Controllers\Orders;

use App\Data\RequestWriters\Order\DeliverOrderItemsRequestWriter;
use App\Models\Order;
use App\Models\StoredItems\StoredItem;
use App\Http\Controllers\Controller;

class OrderItemsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('roles.deny:client,driver,worker');
    }

    public function edit()
    {
        return view('orders.edit-items-list');
    }

    public function update(Order $order)
    {
        $data = new \stdClass();
        $data->order = $order;
        $data->employee = auth()->user();
        $data->storedItems = $this->getStoredItems();

        $writer = new DeliverOrderItemsRequestWriter($data);
        $writer->write();

        return;
    }

    private function getStoredItems()
    {
        return StoredItem::whereIn('id', \request()->get('items'))->get();
    }

    public function storedItems(Order $order)
    {
        return $order->storedItems()->with('info', 'info.item', 'info.owner', 'storageHistory.storage')->get();
    }
}
