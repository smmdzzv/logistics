<?php


namespace App\Http\Controllers\Trips;


use App\Data\RequestWriters\Trips\AssociateToTripRequestWriter;
use App\Data\RequestWriters\Trips\LoadItemsToCarRequestWriter;
use App\Data\RequestWriters\Trips\UnloadItemsToCarRequestWriter;
use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\StoredItems\StoredItem;
use App\Models\Trip;
use Illuminate\Database\Eloquent\Builder;

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
        $data->employee = auth()->user();
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
        $data->storedItems = $this->getTripItemsFromRequest($trip);

        $data->employee = auth()->user();

        $writer = new LoadItemsToCarRequestWriter($data);
        $writer->write();

        return;
    }

    public function editUnloaded(Trip $trip)
    {
        $trip->load('storedItems.info.item', 'storedItems.info.owner', 'storedItems.storageHistory.storage', 'car');
        $branches = Branch::all();
        return view('trips.unload-items', compact('trip', 'branches'));
    }

    public function updateUnloaded(Trip $trip)
    {
        $data = new \stdClass();
        $data->storedItems = $this->getTripItemsFromRequest($trip);
        $data->branch = Branch::findOrFail(request()->input('branch'));
        $data->employee = auth()->user();

        $writer = new UnloadItemsToCarRequestWriter($data);
        $writer->write();

        return;
    }

    private function getTripItemsFromRequest(Trip $trip)
    {
        return StoredItem::whereHas('tripHistory', function (Builder $query) use ($trip) {
            $query->where('trip_id', $trip->id);
        })->whereIn('id', request()->get('storedItems'))->get();
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
