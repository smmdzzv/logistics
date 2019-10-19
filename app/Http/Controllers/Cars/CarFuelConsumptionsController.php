<?php

namespace App\Http\Controllers\Cars;

use App\Data\MassWriters\Car\FuelConsumptionsWriter;
use App\Http\Requests\CarFuelConsumptionsRequest;
use App\Models\Car;
use App\Models\Country;
use App\Models\FuelConsumption;
use App\Http\Controllers\Controller;

class CarFuelConsumptionsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('roles.allow:admin');
    }

    public function edit(Car $car)
    {
        $china = Country::where('name', 'Китай')->first();
        $tjk = Country::where('name', 'Таджикистан')->first();

        $toChina = $car->fuelConsumptions()->where('destination_id', $china->id)->latest()->first();
        $fromChina = $car->fuelConsumptions()->where('destination_id', $tjk->id)->latest()->first();

        if (!$toChina)
            $toChina = new FuelConsumption(['destination_id' => $china->id]);
        if (!$fromChina)
            $fromChina = new FuelConsumption(['destination_id' => $tjk->id]);

        return view('car-fuel-consumption.edit', compact('toChina', 'fromChina', 'car'));
    }

    public function update(Car $car, CarFuelConsumptionsRequest $request)
    {
        $consumptions = [];

        $countries = Country::whereIn('name', ['Таджикистан', 'Китай'])->get();

        $consumptions[] = new FuelConsumption([
            'forEmpty' => $request->input('toChina_forEmpty'),
            'forLoaded' => $request->input('toChina_forLoaded'),
            'forEmptyTrailer' => $request->input('toChina_forEmptyTrailer'),
            'forLoadedTrailer' => $request->input('toChina_forLoadedTrailer'),
            'destination_id' => $countries->firstWhere('name', 'Китай')->id,
            'car_id' => $car->id
        ]);

        $consumptions[] = new FuelConsumption([
            'forEmpty' => $request->input('fromChina_forEmpty'),
            'forLoaded' => $request->input('fromChina_forLoaded'),
            'forEmptyTrailer' => $request->input('fromChina_forEmptyTrailer'),
            'forLoadedTrailer' => $request->input('fromChina_forLoadedTrailer'),
            'destination_id' => $countries->firstWhere('name', 'Таджикистан')->id,
            'car_id' => $car->id
        ]);

        $writer = new FuelConsumptionsWriter($consumptions);
        $writer->write();

        return redirect(route('cars.show', $car));
    }
}
