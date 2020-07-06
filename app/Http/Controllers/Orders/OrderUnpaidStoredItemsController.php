<?php
/**
 *
 * @author Sultonazar Mamadazizov <sultonazar.mamadazizov@mail.ru> on 06.07.2020
 */

namespace App\Http\Controllers\Orders;


use App\Http\Controllers\Controller;
use App\Models\Order;

class OrderUnpaidStoredItemsController extends Controller
{
    public function index(Order $order)
    {
        return $order->storedItems()
            ->with('info', 'info.item', 'info.billingInfo', 'info.owner', 'storageHistory.storage')
            ->unpaid()->get();
    }
}
