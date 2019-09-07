<?php

namespace App\Http\Controllers;

use App\Http\Requests\TariffPriceHistoryRequest;
use App\Tariff;
use App\TariffPriceHistory;
use Illuminate\Http\Request;

class TariffPriceHistoriesController extends Controller
{
    public function index(){
        $histories = TariffPriceHistory::with('tariff')->get();
        return view('tariff-price-histories.index', compact('histories'));
    }

    public function create(){
        $tariffs = Tariff::all();
        return view('tariff-price-histories.create', compact('tariffs'));
    }

    public function store(TariffPriceHistoryRequest $request){
        $data = $request->all();
        $data['branch_id'] = auth()->user()->branch->id;
        return TariffPriceHistory::create($data);
    }

    public function lastByTariff(Tariff $tariff){
        return $tariff->lastPriceHistory();
    }
}
