<?php


namespace App\Http\Controllers\Trips;


use App\Data\MassWriters\Trip\StoredItemTripHistoryWriter;
use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\StoredItems\StoredItem;
use App\Models\StoredItems\StoredItemTripHistory;
use App\Models\Trip;

class TripStoredItemsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('roles.deny:client');
    }

    public function associateToTrip(Trip $trip)
    {
        $storedItems = request()->storedItems;
        $dissociate = [];

        foreach ($trip->storedItems as $stored) {
            if (!in_array($stored->id, $storedItems))
                array_push($dissociate, $stored->id);
        }

        StoredItemTripHistory::where('trip_id', $trip->id)
            ->whereIn('stored_item_id', $dissociate)
            ->forceDelete();

        $existingRecords = StoredItemTripHistory::where('trip_id', $trip->id)
            ->whereIn('stored_item_id', $storedItems)
            ->get();

        $storedItems = array_filter($storedItems,
            function ($stored) use ($existingRecords, $storedItems) {
                foreach ($existingRecords as $history) {
                    if ($history->stored_item_id === $stored)
                        return false;
                }
                return true;
            });

        $items = array();

        foreach ($storedItems as $stored) {
            $items[] = new StoredItemTripHistory([
                'trip_id' => $trip->id,
                'stored_item_id' => $stored
            ]);
        }
        if (count($items) > 0) {
            $writer = new StoredItemTripHistoryWriter($items);
            $writer->write();
        }

        return $trip;
    }

    public function edit(Trip $trip)
    {
        $trip->load('storedItems.info.item', 'storedItems.info.owner', 'car');
        $branches = Branch::all();
        return view('trips.edit-items-list', compact('trip', 'branches'));
    }

    public function availableItems()
    {
        $paginate = request()->input('paginate') ?? 10;
        return StoredItem::with('info.owner', 'info.item')->whereDoesntHave('tripHistory')->paginate($paginate);
    }

    public function availableItemsAtBranch(Branch $branch)
    {
        $paginate = request()->input('paginate') ?? 10;
        return $branch->stores()->first()->storedItems()->whereDoesntHave('tripHistory')->with('info.owner', 'info.item')->paginate($paginate);
    }
}
