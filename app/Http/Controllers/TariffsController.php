<?php

namespace App\Http\Controllers;

use App\Tariff;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TariffsController extends Controller
{
    public function __construct(){
        $this->middleware('auth');
    }

//    protected function validator(array $data)
//    {
//        return Validator::make($data, [
//            'name' => 'required|string|max:255',
//            'description' => ['required', 'string', 'email', 'max:255', 'unique:users'],
//            'password' => ['required', 'string', 'min:8', 'confirmed'],
//            'roles' => ['required', 'array'],
//            'branch' => ['required', 'exists:branches,id']
//        ]);
//    }

    public function lastByTariff(Tariff $tariff){
        return $tariff->lastPriceHistory();
    }

    public function create(){
        $tariffs = Tariff::all();
        return view('tariffs.create', compact('tariffs'));
    }

    public function store(){
        $data = request()->validate([
            'name' => 'required|string|max:255',
            'description' => 'string|max:255|nullable'
        ]);

        return Tariff::create($data);
    }

    public function delete(Tariff $tariff){
        $tariff->delete();
    }
}
