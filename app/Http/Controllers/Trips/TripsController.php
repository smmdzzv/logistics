<?php

namespace App\Http\Controllers\Trips;

use App\Http\Controllers\Controller;
use App\Http\Requests\TripRequest;
use App\Models\Branch;
use App\Models\Car;
use App\Models\Trip;

class TripsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('roles.allow:manager,director,admin');
    }

    public function create()
    {
        $cars = Car::all();
        $branches = Branch::all();
        return view('trips.create', compact('cars', 'branches'));
    }

    public function all()
    {
        $paginate = request()->paginate ?? 10;
        return Trip::with('car', 'driver')->latest()->paginate($paginate);
    }

    public function index()
    {
        return view('trips.index');
    }

    public function show(Trip $trip)
    {
        $trip->load(
            'driver',
            'car',
            'storedItems.info.owner',
            'storedItems.info.item',
            'storedItems.info.item.tariff',
            'storedItems.info.customsCode',
            'storedItems.storageHistory.storage');
        return view('trips.show', compact('trip'));
    }

    public function store(TripRequest $request)
    {
        return Trip::create(array_merge($request->all(), ['status' => 'created']));
    }

    public function edit(Trip $trip)
    {
        $cars = Car::all();
        $branches = Branch::all();
        $trip->load('driver', 'car', 'destinationBranch', 'departureBranch');
        return view('trips.edit', compact('cars', 'trip', 'branches'));
    }

    public function update(TripRequest $request, Trip $trip)
    {
        $trip->fill(array_merge($request->all(), ['status' => 'created']));
        $trip->save();
        return $trip;
    }

    public function storedItems(Trip $trip)
    {
        return $trip->storedItems;
    }
}
