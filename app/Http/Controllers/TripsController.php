<?php

namespace App\Http\Controllers;

use App\Http\Requests\TripRequest;
use App\Models\Branch;
use App\Models\Car;
use App\Models\Trip;
use Illuminate\Http\Request;

class TripsController extends Controller
{
    public function create(){
        $cars = Car::all();
        return view('trips.create', compact('cars'));
    }

    public function show(Trip $trip){
        $trip->load('driver', 'car');
        return view('trips.show', compact('trip'));
    }

    public function store(TripRequest $request){
        return Trip::create($request->all());
    }

    public function edit(Trip $trip){
        $cars = Car::all();
        $branches = Branch::all();
        $trip->load('driver', 'car');
        return view('trips.edit', compact('cars', 'trip','branches'));
    }

    public function update(TripRequest $request, Trip $trip){
        $trip->fill($request->all());
        $trip->save();
        return $trip;
    }

    public function editStoredList(Trip $trip){
        $trip->load('storedItems','car');
        $branches = Branch::all();
        return view('trips.edit-items-list', compact('trip', 'branches'));
    }

    public function storedItems(Trip $trip){
        return $trip->storedItems;
    }
}
