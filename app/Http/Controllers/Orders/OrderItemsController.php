<?php

namespace App\Http\Controllers\Orders;

use App\Models\Order;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class OrderItemsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('roles.deny:client,driver,worker');
    }

    public function edit(){
        return view('orders.edit-items-list');
    }

    public function update(Order $order){
        dd(\request()->all());
    }

    public function storedItems(Order $order){
        return $order->storedItems()->with('info', 'info.item', 'info.owner', 'storageHistory.storage')->get();
    }
}
