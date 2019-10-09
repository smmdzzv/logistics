<?php

namespace App\Http\Controllers;

use App\Http\Requests\TariffPriceHistoryRequest;
use App\Models\Tariff;
use App\Models\TariffPriceHistory;
use Illuminate\Http\Request;

class TariffPriceHistoriesController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');

        $except = ['all', 'index', 'lastByTariff'];

        $this->middleware('roles.allow:admin')->except($except);
        $this->middleware('roles.deny:client')->only($except);
    }

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

    //TODO move to tariff controller
    public function lastByTariff(Tariff $tariff)
    {
        return $tariff->lastPriceHistory;
    }
}
