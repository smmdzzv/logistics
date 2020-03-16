<?php


namespace App\Http\Controllers\StoredItemInfo;


use App\Data\Filters\StoredItemInfoFilter;
use App\Http\Controllers\BaseController;
use App\Models\Branch;
use App\Models\StoredItems\Item;
use App\Models\StoredItems\StoredItemInfo;
use App\Models\Users\Client;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

class StoredItemInfoController extends BaseController
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('roles.allow:manager,storekeeper');
    }

    public function index()
    {
        if (auth()->user()->hasRole('admin'))
            $branches = Branch::all();
        else
            $branches = new Collection([auth()->user()->branch]);
        $items = Item::all();
        return view('stored.infos.index', compact('branches', 'items'));
    }

    private function prepareQuery()
    {
        $query = StoredItemInfo::with('owner', 'item', 'tariff', 'billingInfo', 'storedItems.storageHistory.storage.branch')->whereHas('storedItems');
        $filter = new StoredItemInfoFilter(request()->all(), $query);
        return $filter->filter();
    }

    //TODO refactor
    public function storedItemInfos()
    {
//        $query = StoredItemInfo::with('owner', 'item', 'tariff', 'billingInfo', 'storedItems.storageHistory.storage.branch')->whereHas('storedItems');
//        $filter = new StoredItemInfoFilter(request()->all(), $query);
//        $query = $filter->filter();
        $query = $this->prepareQuery();
        return $query->paginate($this->pagination());
    }

    public function getClientStat()
    {
        $dateTo = request()->get('dateFrom');
        request()->request->remove('dateFrom');
        $client = Client::where('code', request()->get('client'))->firstOrFail();
        $query = $this->prepareQuery();
        return $client->getStoredItemInfosStat($dateTo, $query)->toJson();
    }

//    TODO remove this
//    public function availableStoredItemInfos()
//    {
//
//        $query = StoredItemInfo::with('owner', 'item', 'tariff', 'billingInfo', 'storedItems.storageHistory.storage.branch')->whereHas('storedItems');
//        $filter = new StoredItemInfoFilter(request()->all(), $query);
//        $query = $filter->filter();
//
//        return $query->paginate($this->pagination());
//    }
}
