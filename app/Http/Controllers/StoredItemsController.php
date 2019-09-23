<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\StoredItem;
use App\Models\Trip;
use Illuminate\Http\Request;

class StoredItemsController extends Controller
{
    public function index()
    {
        $branches = Branch::all();
        return view('stored.index', compact('branches'));
    }

    public function all()
    {
        return StoredItem::with(['owner', 'item'])->paginate(20);
    }

    public function filteredByBranch(Branch $branch)
    {
        if (isset($branch)) {
            return $branch->storedItems()->with(['owner', 'item'])->paginate(20);
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
