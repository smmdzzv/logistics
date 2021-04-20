<?php

namespace App\Http\Controllers;

use App\Models\Car;
use App\Http\Requests\CarRequest;
use App\Models\CarProvider;

class CarsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $cars = Car::all();
        return view('cars.index', compact('cars'));
    }

    public function all()
    {
        return Car::all();
    }

    public function create()
    {
        $carProviders = CarProvider::all();
        return view('cars.create', compact('carProviders'));
    }

    public function show(Car $car)
    {
        $car->load('toChinaConsumption', 'fromChinaConsumption');
        return view('cars.show', compact('car'));
    }

    public function edit(Car $car)
    {
        $carProviders = CarProvider::all();
        return view('cars.edit', compact('car', 'carProviders'));
    }

    public function destroy(Car $car)
    {
        return $car->delete();
    }

    public function store(CarRequest $request)
    {
        $car = Car::create($request->validated());
        return redirect(route('cars.show', $car));
    }

    public function update(CarRequest $request)
    {
        $data = $request->validated();
        $car = Car::findOrFail($data['id']);
        $car->fill($data);
        $car->save();
        return redirect(route('cars.show', $car));
    }
}
