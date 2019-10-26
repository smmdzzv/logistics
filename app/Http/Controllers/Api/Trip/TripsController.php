<?php

namespace App\Http\Controllers\Api\Trip;

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

    private function getTripItemFromRequest(Trip $trip)
    {
        $item = StoredItem::whereHas('tripHistory', function (Builder $query) use ($trip) {
            $query->where('trip_id', $trip->id);
        })->where('id', request()->get('stored_item'))->first();

        if (!$item)
            abort(400, 'Не переданы идентификаторы товаров');

        return $item;
    }
}
