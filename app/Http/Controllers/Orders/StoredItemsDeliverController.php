<?php
/**
 *
 * @author Sultonazar Mamadazizov <sultonazar.mamadazizov@mail.ru> on 07.07.2020
 */

namespace App\Http\Controllers\Orders;


use App\Http\Controllers\Controller;
use App\Models\Order\StoredItemsSelection;

class StoredItemsDeliverController extends Controller
{
    public function index()
    {
        $orderPayment = request()->get('payment') ?
            StoredItemsSelection::with('order.owner', 'paidItems.storedItem')
                ->where('payment_id', request()->get('payment'))
                ->first() : null;

        return view('orders.edit-items-list', compact('orderPayment'));
    }
}
