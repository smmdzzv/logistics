<?php
/**
 *
 * @author Sultonazar Mamadazizov <sultonazar.mamadazizov@mail.ru> on 11.07.2020
 */

namespace App\Http\Controllers\Orders;


use App\Data\Filters\OrderFilter;
use App\Http\Controllers\BaseController;
use App\Models\Order;

class FilteredOrdersController extends BaseController
{
    public function index()
    {
        $branches = $this->getBranches()->map(function ($branch) {
            return $branch->id;
        });
        $query = Order::whereIn('branch_id', $branches)
            ->with(['owner', 'creator', 'storedItemInfos', 'branch'])
            ->latest();
        $filter = new OrderFilter(request()->all(), $query);
        $query = $filter->filter();

        return $query->paginate($this->pagination());
    }
}
