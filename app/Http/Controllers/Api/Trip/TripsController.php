<?php

namespace App\Http\Controllers\Api\Trip;

use App\Data\RequestWriters\Trips\ChangeItemsTripRequest;
use App\Data\RequestWriters\Trips\LoadItemsToCarRequestWriter;
use App\Data\RequestWriters\Trips\UnloadItemsFromCarRequestWriter;
use App\Models\Branch;
use App\Models\StoredItems\StoredItem;
use App\Models\Trip;
use Illuminate\Database\Eloquent\Builder;
use App\Http\Controllers\Controller;
use Illuminate\Support\Collection;

class TripsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
        $this->middleware('roles.allow:employee');
    }

    public function activeTrips()
    {
        return Trip::where('status', 'active')->latest()->get();
    }

    public function unloadItem(Trip $trip)
    {
        $data = new \stdClass();
        $data->storedItems = new Collection();
        $data->storedItems->push($this->getTripItemFromRequest($trip));
        $data->branch = Branch::findOrFail(auth()->user()->branch_id);
        $data->employee = auth()->user();

        $writer = new UnloadItemsFromCarRequestWriter($data);
        $writer->write();

        return;
    }

    public function loadItem(Trip $trip){
        $data = new \stdClass();

        $data->storedItems = new Collection();
        $data->storedItems->push($this->getTripItemFromRequest($trip));

        $data->employee = auth()->user();

        $writer = new LoadItemsToCarRequestWriter($data);
        $writer->write();

        return;
    }

    public function transferItem(Trip $trip)
    {
        $data = new \stdClass();
        $data->trip = $trip;
        $data->targetTrip = Trip::findOrFail(request('targetTrip'));
        $data->employee = auth()->user();
        $data->storedItems = new Collection();
        $data->storedItems->push($this->getTripItemFromRequest($trip));

        $writer = new ChangeItemsTripRequest($data);
        $writer->write();
    }

    private function getTripItemFromRequest(Trip $trip)
    {
        $item = StoredItem::whereHas('tripHistory', function (Builder $query) use ($trip) {
            $query->where('trip_id', $trip->id);
        })->where('code', request()->get('stored_item'))->first();

        if (!$item)
            abort(400, 'Не передан код товаров');
        return $item;
    }
}
