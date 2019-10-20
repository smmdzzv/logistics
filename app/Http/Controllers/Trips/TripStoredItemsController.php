<?php


namespace App\Http\Controllers\Trips;


use App\Data\RequestWriters\Trips\AssociateToTripRequestWriter;
use App\Data\RequestWriters\Trips\LoadItemsToCarRequestWriter;
use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\StoredItems\StoredItem;
use App\Models\Trip;

class TripStoredItemsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('roles.deny:client,cashier,driver')->except('loadToCar');
        $this->middleware('roles.allow:admin')->only('editLoaded', 'updateLoaded');
    }

    public function associateToTrip(Trip $trip)
    {
        $data = new \stdClass();
        $data->trip = $trip;
        $data->storedItems = request()->storedItems;
        $writer = new AssociateToTripRequestWriter($data);
        $writer->write();

        return $trip;
    }

    public function editLoaded(Trip $trip)
    {
        $trip->load('storedItems.info.item', 'storedItems.info.owner', 'storedItems.storageHistory.storage', 'car');
        return view('trips.load-items', compact('trip'));
    }

    public function updateLoaded(Trip $trip)
    {
        $data = new \stdClass();
        $data->storedItems = StoredItem::whereIn('id', request()->all())->get();

        $writer = new LoadItemsToCarRequestWriter($data);
        $writer->write();

        return;
    }

    public function edit(Trip $trip)
    {
        $trip->load('storedItems.info.item', 'storedItems.info.owner', 'storedItems.storageHistory.storage', 'car');
        $branches = Branch::all();
        return view('trips.edit-items-list', compact('trip', 'branches'));
    }

    public function availableItems()
    {
        $paginate = request()->input('paginate') ?? 10;
        return StoredItem::whereDoesntHave('tripHistory')->with('info.owner', 'info.item', 'storageHistory.storage')->paginate($paginate);
    }

    public function availableItemsAtBranch(Branch $branch)
    {
        $paginate = request()->input('paginate') ?? 10;
        return $branch->stores()->first()->storedItems()->whereDoesntHave('tripHistory')->with('info.owner', 'info.item', 'storageHistory.storage')->paginate($paginate);
    }
}
