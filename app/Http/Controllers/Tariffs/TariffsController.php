<?php

namespace App\Http\Controllers\Tariffs;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\Tariff;

class TariffsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');

        $exceptions = ['pricing'];

        $this->middleware('roles.allow:admin')->except($exceptions);
        $this->middleware('roles.allow:manager')->only($exceptions);
    }

    public function index()
    {
        $tariffs = Tariff::with('branch')->get();
        $branches = Branch::all();
        return view('tariffs.create', compact('tariffs', 'branches'));
    }

//    public function create(){
//        $tariffs = Tariff::all();
//        return view('tariffs.create', compact('tariffs'));
//    }

    public function store()
    {
        $data = request()->validate([
            'name' => 'required|string|max:255',
            'description' => 'string|max:255|nullable',
            'branch_id' => 'required|exists:branches,id'
        ]);

        return Tariff::create($data)->load('branch');
    }

    public function destroy(Tariff $tariff)
    {
        $tariff->delete();
    }

    public function pricing(Tariff $tariff)
    {
        return $tariff->lastPriceHistory;
    }
}
