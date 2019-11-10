<?php


namespace App\Http\Controllers\Trips;


use App\Data\RequestWriters\Trips\AssociateToTripRequestWriter;
use App\Data\RequestWriters\Trips\ChangeItemsTripRequest;
use App\Data\RequestWriters\Trips\LoadItemsToCarRequestWriter;
use App\Data\RequestWriters\Trips\UnloadItemsFromCarRequestWriter;
use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\StoredItems\StoredItem;
use App\Models\Trip;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;

class TripStoredItemsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $adminOnly = ['editLoaded', 'updateLoaded', 'editUnloaded', 'updateUnloaded', 'exchangeItems', 'changeItemsTrip'];
        $this->middleware('roles.deny:client,cashier,driver')->except($adminOnly);
        $this->middleware('roles.allow:admin')->only($adminOnly);
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

    private function getTripItemsFromRequest(Trip $trip)
    {
        return StoredItem::whereHas('tripHistory', function (Builder $query) use ($trip) {
            $query->where('trip_id', $trip->id);
        })->whereIn('id', request()->get('storedItems'))->get();
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

        $writer = new UnloadItemsFromCarRequestWriter($data);
        $writer->write();

        return;
    }

    public function edit(Trip $trip)
    {
        $trip->load('storedItems.info.item', 'storedItems.info.owner', 'storedItems.storageHistory.storage', 'car');
        $branches = Branch::all();
        return view('trips.edit-items-list', compact('trip', 'branches'));
    }

    public function exchangeItems(Trip $trip)
    {

        $data = new \stdClass();
        $data->trip = $trip;
        $data->targetTrip = Trip::findOrFail(request()->get('targetTrip'));
        $data->employee = auth()->user();
        $data->storedItems = $this->getTripItemsFromRequest($trip);

        $writer = new ChangeItemsTripRequest($data);
        $writer->write();
    }

    public function changeItemsTrip(Trip $trip)
    {
        $trips = Trip::where('status', '!=', 'finished')->get();
        $trips = $trips->reject(function ($item) use ($trip) {
            return $trip->id === $item->id;
        });
        $trip->load('loadedItems.info.item', 'loadedItems.info.owner', 'loadedItems.storageHistory.storage');
        return view('trips.change-items-trip', compact('trip', 'trips'));
    }

    public function changeStatus(Trip $trip){
        $status = request()->input('status');

        if($status === 'active')
            $trip->departureAt = Carbon::now();
        if($status === 'finished')
            $trip->returnedAt = Carbon::now();
        $trip->status = $status;

        $trip->save();

        return redirect(route('trips.show', $trip));
    }

    public function generate(){
        return [];
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
