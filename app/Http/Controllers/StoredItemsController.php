<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\StoredItems\StoredItem;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

class StoredItemsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');

        $this->middleware("roles.allow:cashier,storekeeper,manager,admin");
    }

//    public function index()
//    {
//        if (auth()->user()->hasRole('admin'))
//            $branches = Branch::all();
//        else
//            $branches = new Collection([auth()->user()->branch]);
//        return view('stored.index', compact('branches'));
//    }

    public function all()
    {
        return StoredItem::with('info.owner', 'info.item', 'storageHistory.storage')->latest()->paginate(10);
    }

    public function show($storedItem)
    {
        $storedItem = StoredItem::withTrashed()->find($storedItem);
        $storedItem->load('info', 'info.owner', 'info.billingInfo');
        $storageHistories = $storedItem->storageHistories()->latest()
            ->withTrashed()->with('storage', 'deletedBy', 'registeredBy')->get();
        $tripHistories = $storedItem->tripHistory()->latest()
            ->withTrashed()->with('trip', 'deletedBy', 'registeredBy', 'loadedBy')->get();
        return view('stored.show', compact('storedItem', 'storageHistories', 'tripHistories'));
    }

    public function filteredByBranch(Branch $branch)
    {
        if (isset($branch)) {
            return StoredItem::with('info.owner', 'info.item', 'storageHistory.storage')
                ->whereHas('storage', function (Builder $query) use ($branch) {
                    $query->where('branch_id', $branch->id)->where('deleted_at', null);
                })
                ->latest()
                ->paginate(20);
        } else abort(404, 'Филиал не найден');
    }
}
