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
        return StoredItem::with(['info.owner', 'info.item'])->latest()->paginate(10);
    }

    public function filteredByBranch(Branch $branch)
    {
        if (isset($branch)) {
            return $branch->storedItems()->with(['info.owner', 'info.item'])->latest()->paginate(10);
        } else abort(404, 'Филиал не найден');
    }

    public function associateToTrip(Trip $trip)
    {
        $storedItems = request()->storedItems;
        $dissociate = [];

        foreach ($trip->storedItems as $stored) {
            if (!in_array($stored->id, $storedItems))
                array_push($dissociate, $stored->id);
        }

        StoredItem::whereIn('id', $dissociate)->update(['tripId' => null]);
        StoredItem::whereIn('id', $storedItems)->update(['tripId' => $trip->id]);

        return $trip;
    }
}
