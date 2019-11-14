<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\StoredItems\StoredItem;
use App\Models\Trip;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class StoredItemsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');

        $this->middleware("roles.deny:client");
    }

    public function index()
    {
        $branches = Branch::all();
        return view('stored.index', compact('branches'));
    }

    public function all()
    {
        return StoredItem::with('info.owner', 'info.item', 'storageHistory.storage')->latest()->paginate(10);
    }

    public function show(StoredItem $storedItem){
        $storedItem->load('info');
        $storageHistories = $storedItem->storageHistories()->latest()->withTrashed()->with('storage', 'deletedBy', 'registeredBy')->get();
        $tripHistories = $storedItem->tripHistory()->latest()->withTrashed()->with('trip', 'deletedBy', 'registeredBy', 'loadedBy')->get();
        return view('stored.show', compact('storedItem', 'storageHistories', 'tripHistories'));
    }

    public function filteredByBranch(Branch $branch)
    {
        if (isset($branch)) {
            return StoredItem::with('info.owner', 'info.item', 'storageHistory.storage')
                ->whereHas('storage', function (Builder $query) use($branch) {
                    $query->where('branch_id', $branch->id);
                })
                ->latest()
                ->paginate(20);
        } else abort(404, 'Филиал не найден');
    }
}
