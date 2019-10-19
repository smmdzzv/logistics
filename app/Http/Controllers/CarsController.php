<?php

namespace App\Http\Controllers;

use App\Models\Car;
use App\Http\Requests\CarRequest;
use Illuminate\Http\Request;

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
        return view('cars.create');
    }

    public function show(Car $car)
    {
        $car->load('toChinaConsumption', 'fromChinaConsumption');
        return view('cars.show', compact('car'));
    }

    public function edit(Car $car)
    {
        return view('cars.edit', compact('car'));
    }

    public function destroy(Car $car)
    {
        $car->delete();
        return;
    }

    public function store(CarRequest $request)
    {
        $car = Car::create($request->all());
        return redirect(route('cars.show', $car));
    }

    public function update(CarRequest $request)
    {
        $data = $request->all();
        $car = Car::findOrFail($data['id']);
        $car->fill($data);
        $car->save();
        return redirect(route('cars.show', $car));
    }
}
