<?php

namespace App\Http\Controllers\Till;

use App\Http\Controllers\Controller;
use App\Models\Currency;
use App\Models\Till\ExchangeRate;
use Illuminate\Http\Request;

class MoneyExchangesController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');

        $this->middleware('roles.deny:client');
        $this->middleware('roles.allow:admin,cashier')->except('exchangeRate', 'index');
    }

    public function exchangeRate($from, $to)
    {
        return ExchangeRate::where('from_currency_id', $from)
            ->where('to_currency_id', $to)
            ->dailyRate()
            ->latest()
            ->firstOrFail();
    }

    public function index()
    {
        $rates = ExchangeRate::latest()->dailyRate()->paginate(25);
        return view('till.money-exchanges.index', compact('rates'));
    }

    public function create()
    {
        $currencies = Currency::all();
        return view('till.money-exchanges.create', compact('currencies'));
    }

    public function store(Request $request)
    {
        $data = $request->validate($this->rules());
        ExchangeRate::create($data);
        return redirect(route('money-exchanges.index'));
    }

    private function rules(): array
    {
        return [
            'from_currency_id' => 'required|exists:currencies,id',
            'to_currency_id' => 'required|exists:currencies,id|different:from_currency_id',
            'coefficient' => 'required|numeric'
        ];
    }
}
