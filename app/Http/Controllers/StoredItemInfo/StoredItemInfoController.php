<?php


namespace App\Http\Controllers\StoredItemInfo;


use App\Data\Filters\StoredItemInfoFilter;
use App\Http\Controllers\BaseController;
use App\Models\Branch;
use App\Models\StoredItems\StoredItemInfo;
use Illuminate\Database\Eloquent\Builder;

class StoredItemInfoController extends BaseController
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('roles.allow:client,manager,storekeeper');
    }

    //TODO refactor
    public function storedItemInfos()
    {
        if (!auth()->user()->hasRole('admin'))
            $branch = auth()->user()->branch;
        else if (request('branch'))
            $branch = Branch::find(request('branch'));

        if (isset($branch))
            $query = StoredItemInfo::with(['storedItems' => function ($query) use ($branch) {
                $query->whereHas('storage', function (Builder $query) use ($branch) {
                    $query->where('branch_id', $branch->id)->where('deleted_at', null);
                });
            }, 'storedItems.storageHistory.storage.branch', 'owner', 'item'])->latest();
        else
            $query = StoredItemInfo::with('owner', 'item','storedItems.storageHistory.storage.branch')->latest();


        $filter = new StoredItemInfoFilter(request()->all(), $query);
        $query = $filter->filter();

        return $query->paginate($this->pagination());
    }

    public function availableStoredItemInfos()
    {
        if (!auth()->user()->hasRole('admin'))
            $branch = auth()->user()->branch;
        else if (request('branch'))
            $branch = Branch::find(request('branch'));

        if (isset($branch))
            $query = StoredItemInfo::with(['storedItems' => function ($query) use ($branch) {
                $query->available()->whereHas('storage', function (Builder $query) use ($branch) {
                    $query->where('branch_id', $branch->id)->where('deleted_at', null);
                });
            }, 'storedItems.storageHistory.storage.branch', 'owner'])->latest();
        else
            $query = StoredItemInfo::with(['owner', 'item', 'storedItems' => function ($query) {
                $query->available();
            }, 'storedItems.storageHistory.storage.branch'])->latest();


        $filter = new StoredItemInfoFilter(request()->all(), $query);
        $query = $filter->filter();

        return $query->paginate($this->pagination());
    }
}