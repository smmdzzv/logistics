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
        $query = StoredItemInfo::with(
            'owner:id,code',
            'item:id,name',
            'tariff:id,name',
            'billingInfo',
            'storedItems',
            'storedItems.storageHistory.storage'
        )
            ->whereHas('storedItems');

        $filter = new StoredItemInfoFilter(request()->all(), $query);
        return $filter->filter();
    }

    //TODO count relations and group on server side if needed
    public function storedItemInfos()
    {
        $query = $this->prepareQuery();
        $paginator = $query->paginate($this->pagination());
        $paginator->getCollection()->transform(function ($value) {
            foreach ($value->storedItems as $storedItem) {
                //TODO refactor -> create methods in BaseModel
                unset($storedItem->updated_at);
                unset($storedItem->deleted_at);
                unset($storedItem->created_by_id);
                unset($storedItem->updated_by_id);
                unset($storedItem->deleted_by_id);

                if ($storedItem->storageHistory)
                {
                    unset($storedItem->storageHistory->stored_item_id);
                    unset($storedItem->storageHistory->registeredById);
                    unset($storedItem->storageHistory->deletedById);
                    unset($storedItem->storageHistory->storage_id);
                    unset($storedItem->storageHistory->created_at);
                    unset($storedItem->storageHistory->updated_at);
                    unset($storedItem->storageHistory->deleted_at);
                    unset($storedItem->storageHistory->created_by_id);
                    unset($storedItem->storageHistory->updated_by_id);
                    unset($storedItem->storageHistory->deleted_by_id);
                    unset($storedItem->storageHistory->storage->branch);

                    unset($storedItem->storageHistory->storage->updated_at);
                    unset($storedItem->storageHistory->storage->deleted_at);
                    unset($storedItem->storageHistory->storage->created_by_id);
                    unset($storedItem->storageHistory->storage->updated_by_id);
                    unset($storedItem->storageHistory->storage->deleted_by_id);
                    unset($storedItem->storageHistory->storage->branch_id);
                }

                unset($storedItem->stored_item_info_id);
                unset($storedItem->updated_at);
                unset($storedItem->deleted_at);
                unset($storedItem->created_by_id);
                unset($storedItem->updated_by_id);
                unset($storedItem->deleted_by_id);
            };

            return $value;
        });

        return $paginator;
    }

    private function hideAttr(array $keys, $model)
    {
        foreach ($keys as $key) {
            unset($model[$key]);
        }
    }

    public function getClientStat()
    {
        $dateTo = request()->get('dateFrom');
        request()->request->remove('dateFrom');
        $client = Client::where('code', request()->get('client'))->firstOrFail();
        $query = $this->prepareQuery();
        return $client->getStoredItemInfosStat($dateTo, $query)->toJson();
    }
}
