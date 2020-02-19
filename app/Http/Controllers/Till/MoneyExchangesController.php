<?php

namespace App\Http\Controllers\Till;

use App\Data\RequestWriters\Payments\ExchangeMoneyRequestWriter;
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
    }

    public function exchangeRate($from, $to)
    {
        return ExchangeRate::where('from_currency_id', $from)
            ->where('to_currency_id', $to)
            ->latest()
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
        ExchangeRate::create($data);
        return redirect(route('money-exchanges.create'));
    }

    private function rules()
    {
        return [
            'from_currency_id' => 'required|exists:currencies,id',
            'to_currency_id' => 'required|exists:currencies,id|different:from_currency_id',
            'coefficient' => 'required|numeric'
        ];
    }

//    public function exchanger(){
//        $currencies = Currency::all();
//        return view('till.money-exchanges.exchanger', compact('currencies'));
//    }

//    public function exchange()
//    {
//        $data = new \stdClass();
//        $data->from = \request('from');
//        $data->to = \request('to');
//        $data->amount = \request('amount');
//        $data->comment = \request('comment');
//        $data->cashier = auth()->user();
//
//        $writer = new ExchangeMoneyRequestWriter($data);
//        $writer->write();
//        return;
//    }
}
