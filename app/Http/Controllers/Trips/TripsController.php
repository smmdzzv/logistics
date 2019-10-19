<?php

namespace App\Http\Controllers\Trips;

use App\Http\Controllers\Controller;
use App\Http\Requests\TripRequest;
use App\Models\Branch;
use App\Models\Car;
use App\Models\Trip;
use Carbon\Carbon;
use Illuminate\Http\Request;

class TripsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('roles.allow:employee');
    }

    public function create()
    {
        $cars = Car::all();
        return view('trips.create', compact('cars'));
    }

    public function all()
    {
        $paginate = request()->paginate ?? 10;
        return Trip::with('car', 'driver')->paginate($paginate);
    }

    public function index(){
        return view('trips.index');
    }

    public function show(Trip $trip)
    {
        $trip->load('driver', 'car', 'storedItems.info.owner', 'storedItems.info.item', 'storedItems.storageHistory.storage');
        $trip->isEditable = $trip->departureDate > Carbon::now();
        return view('trips.show', compact('trip'));
    }

    public function store(TripRequest $request)
    {
        return Trip::create($request->all());
    }

    public function edit(Trip $trip)
    {
        $cars = Car::all();
        $branches = Branch::all();
        $trip->load('driver', 'car');
        return view('trips.edit', compact('cars', 'trip', 'branches'));
    }

    public function update(TripRequest $request, Trip $trip)
    {
        $trip->fill($request->all());
        $trip->save();
        return $trip;
    }

    public function storedItems(Trip $trip)
    {
        return $trip->storedItems;
    }
}
