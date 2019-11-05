<?php

namespace App\Http\Controllers\Till;

use App\Data\RequestWriters\Payments\ExchangeMoneyRequestWriter;
use App\Http\Controllers\Controller;
use App\Models\Currency;
use App\Models\Till\MoneyExchange;
use App\User;
use Illuminate\Http\Request;

class MoneyExchangesController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');

        $this->middleware('roles.deny:client');
    }

    public function exchangeRate($from, $to)
    {
        return MoneyExchange::with('toCurrency')
            ->where('from', $from)
            ->where('to', $to)
            ->firstOrFail();
    }

    public function create()
    {
        $currencies = Currency::all();
        return view('till.money-exchanges.create', compact('currencies'));
    }

    public function store(Request $request)
    {
        $data = $request->validate($this->rules());
        MoneyExchange::create($data);
        return redirect(route('money-exchanges.create'));
    }

    private function rules()
    {
        return [
            'from' => 'required|exists:currencies,id',
            'to' => 'required|exists:currencies,id|different:from',
            'coefficient' => 'required|numeric'
        ];
    }

    public function exchanger(){
        $currencies = Currency::all();
        return view('till.money-exchanges.exchanger', compact('currencies'));
    }

    public function exchange()
    {
        $data = new \stdClass();
        $data->from = \request('from');
        $data->to = \request('to');
        $data->amount = \request('amount');
        $data->cashier = auth()->user();
        $data->client = User::find(\request('client'));

        $writer = new ExchangeMoneyRequestWriter($data);
        $writer->write();
        return;
    }
}
