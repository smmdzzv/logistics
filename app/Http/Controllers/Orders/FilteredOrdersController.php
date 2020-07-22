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

        $query = Order::select([
            'id', 'totalWeight', 'totalCubage', 'totalPrice', 'totalDiscount', 'created_by_id', 'created_at', 'branch_id', 'owner_id'
        ])->whereIn('branch_id', $branches)
            ->with(['owner:id,code,name', 'creator:id,code,name', 'branch:id,name'])
            ->withCount('storedItemInfos')
            ->latest();

        $filter = new OrderFilter(request()->all(), $query);

        $query = $filter->filter();

        return $query->paginate($this->pagination());
    }
}
