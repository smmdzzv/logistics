<?php


namespace App\Http\Controllers\StoredItemInfo;


use App\Http\Controllers\BaseController;
use App\Models\StoredItems\StoredItemInfo;

class StoredItemInfoController extends BaseController
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('roles.allow:client,manager,storekeeper');
    }

    public function storedItemInfos(){
        $query = StoredItemInfo::with('owner', 'item', 'storedItems.storageHistory.storage.branch')->latest();

//        $filter = new StoredItemInfoFilter(request()->all(), $query);
//        $query = $filter->filter();

        return $query->paginate($this->pagination());
    }
}