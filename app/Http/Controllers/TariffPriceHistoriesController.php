<?php

namespace App\Http\Controllers;

use App\Http\Requests\TariffPriceHistoryRequest;
use App\Models\Tariff;
use App\Models\TariffPriceHistory;
use Illuminate\Http\Request;

class TariffPriceHistoriesController extends Controller
{
    public function all()
    {
        $paginate = request()->paginate ?? 10;
        return TariffPriceHistory::with('tariff')
            ->orderBy('created_at', 'desc')
            ->paginate($paginate);
    }

    public function index()
    {
        return view('tariff-price-histories.index');
    }

    public function create()
    {
        $tariffs = Tariff::all();
        return view('tariff-price-histories.create', compact('tariffs'));
    }

    public function store(TariffPriceHistoryRequest $request)
    {
        $data = $request->all();
        $data['branch_id'] = auth()->user()->branch->id;
        TariffPriceHistory::create($data);
        return redirect()->route('tariff-price-histories.index');
    }

    public function lastByTariff(Tariff $tariff)
    {
        return $tariff->lastPriceHistory;
    }
}
