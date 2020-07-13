<?php
/**
 *
 * @author Sultonazar Mamadazizov <sultonazar.mamadazizov@mail.ru> on 13.07.2020
 */

namespace App\Http\Controllers\Orders;


use App\Http\Controllers\BaseController;
use App\Models\StoredItems\StoredItem;

class OrderLabelsPrintController extends BaseController
{
    public function show($order)
    {
        $storedItems = StoredItem::whereHas('info', function ($query) use ($order) {
            $query->where('order_id', $order);
        })->with('info.owner', 'info.item')->get();

        return view('print.order-labels', compact('storedItems'));
    }

}
