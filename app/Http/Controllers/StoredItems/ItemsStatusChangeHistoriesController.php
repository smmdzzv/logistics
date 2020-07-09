<?php
/**
 *
 * @author Sultonazar Mamadazizov <sultonazar.mamadazizov@mail.ru> on 10.07.2020
 */

namespace App\Http\Controllers\StoredItems;


use App\Http\Controllers\BaseController;
use App\Models\StoredItems\ItemsStatusChangeHistory;

class ItemsStatusChangeHistoriesController extends BaseController
{
    public function index()
    {
        $histories = ItemsStatusChangeHistory::with('creator')->paginate(10);
        return view('stored.status-change-histories-index', compact('histories'));
    }
}
