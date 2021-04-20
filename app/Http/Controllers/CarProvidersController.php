<?php
/**
 * @author Sultonazar Mamadazizov <sultonazar.mamadazizov@mail.ru>
 */

namespace App\Http\Controllers;


use App\Models\CarProvider;
use Illuminate\Validation\ValidationException;

class CarProvidersController extends BaseController
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('roles.allow');
    }

    public function index()
    {
        $carProviders = CarProvider::all();
        return view('car-providers.index', compact('carProviders'));
    }

    public function create()
    {
        return view('car-providers.create');
    }

    /**
     * @throws ValidationException
     */
    public function store()
    {
        return CarProvider::create($this->getValidatedData());
    }

    public function update(CarProvider $carProvider)
    {
        $carProvider->update($this->getValidatedData());
        return $carProvider;
    }

    public function destroy(CarProvider $carProvider)
    {
        return $carProvider->delete();
    }

    private function getValidatedData(): array
    {
        return request()->validate([
            'name' => 'required|string'
        ]);
    }
}
