<?php

namespace App\Http\Controllers;

use App\Models\Tariff;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TariffsController extends Controller
{
    public function __construct(){
        $this->middleware('auth');
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
