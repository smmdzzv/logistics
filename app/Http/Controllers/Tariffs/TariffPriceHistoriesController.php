<?php
/**
 *
 * @author Sultonazar Mamadazizov <sultonazar.mamadazizov@mail.ru>
 */

namespace App\Http\Controllers\Tariffs;

use App\Data\Dto\Tariff\TariffPriceHistoryDto;
use App\Http\Controllers\Controller;
use App\Http\Requests\TariffPriceHistoryRequest;
use App\Models\Tariff;
use App\Models\TariffPriceHistory;
use App\Services\Tariff\TariffPriceHistoryService;

class TariffPriceHistoriesController extends Controller
{
    private TariffPriceHistoryService $service;

    public function __construct(TariffPriceHistoryService $service)
    {
        $this->middleware('auth');

        $this->service = $service;

        $except = ['all', 'index', 'lastByTariff'];

        $this->middleware('roles.allow:admin')->except($except);
        $this->middleware('roles.deny:client')->only($except);
    }

    public function all()
    {
        $paginate = request()->paginate ?? 10;
        return TariffPriceHistory::with('tariff')
            ->whereHas('tariff')
            ->latest()
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
        $this->service->store(new TariffPriceHistoryDto($data));
        return redirect()->route('tariff-price-histories.index');
    }

    public function edit(TariffPriceHistory $history)
    {
        $history->load('tariff');
        $tariffs = Tariff::all();
        return view('tariff-price-histories.edit', compact('history', 'tariffs'));
    }

    public function update(TariffPriceHistory $history, TariffPriceHistoryRequest $request)
    {
        $this->service->update($history, new TariffPriceHistoryDto($request->all()));
        return redirect()->route('tariff-price-histories.index');
    }

    public function destroy(TariffPriceHistory $history)
    {
        $this->service->destroy($history);
        return;
    }
}

