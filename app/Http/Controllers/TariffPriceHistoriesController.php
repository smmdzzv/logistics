<?php

namespace App\Http\Controllers;

use App\Http\Requests\TariffPriceHistoryRequest;
use App\Models\Tariff;
use App\Models\TariffPriceHistory;

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
        $selectedTariff = $tariffs->firstWhere('id', \request()->get('tariff'));
        return view('tariff-price-histories.create', compact('tariffs', 'selectedTariff'));
    }

    public function store(TariffPriceHistoryRequest $request)
    {
        $data = $request->all();
        $data['branch_id'] = auth()->user()->branch->id;
        TariffPriceHistory::create($data);
        return redirect()->route('tariff-price-histories.index');
    }

    public function edit(TariffPriceHistory $history)
    {
        $history->load('tariff');
        $tariffs = Tariff::all();
        return view('tariff-price-histories.edit', compact('history', 'tariffs'));
    }

    public function update(TariffPriceHistoryRequest $request)
    {
        TariffPriceHistory::find($request->get('id'))->update($request->all());
        return redirect()->route('tariff-price-histories.index');
    }

    public function destroy(TariffPriceHistory $history)
    {
        if ($history->billingInfos->count() > 0)
            abort(422, 'Невозможно удалить расценки, использованные для расчета заказов');

        $history->delete();
        return;
    }
}

