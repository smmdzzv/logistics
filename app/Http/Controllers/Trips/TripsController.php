<?php

namespace App\Http\Controllers\Trips;

use App\Http\Controllers\Controller;
use App\Http\Requests\TripRequest;
use App\Models\Branch;
use App\Models\Car;
use App\Models\Trip;
use Carbon\Carbon;

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
            'toConsumption',
            'fromConsumption',
            'storedItems.info.owner',
            'storedItems.info.item',
            'storedItems.info.item',
            'storedItems.info.tariff',
            'storedItems.info.customsCode',
            'storedItems.info.billingInfo',
            'storedItems.storageHistory.storage');

        $calculatedConsumptionTo = $trip->getCalculatedConsumptionTo();
        $calculatedConsumptionFrom = $trip->getCalculatedConsumptionFrom();

        return view('trips.show', compact('trip', 'calculatedConsumptionTo', 'calculatedConsumptionFrom'));
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

    public function changeStatus(Trip $trip)
    {
        $status = request()->input('status');

        if ($status === 'active')
            $trip->departureAt = Carbon::now();
        if ($status === 'finished') {
            $trip->returnedAt = Carbon::now();
            $trip->mileageAfter = request()->input('mileageAfter');
            $trip->totalFuelConsumption = request()->input('consumption');
            $trip->car->fuelAmount -= request()->input('consumption');
            $trip->car->saveOrFail();
        }

        $trip->status = $status;

        $trip->save();

        return redirect(route('trips.show', $trip));
    }
}
