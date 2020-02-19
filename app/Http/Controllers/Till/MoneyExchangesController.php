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
        return ExchangeRate::with('toCurrency')
            ->where('fromCurrency', $from)
            ->where('toCurrency', $to)
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
            'from' => 'required|exists:currencies,id',
            'to' => 'required|exists:currencies,id|different:from',
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
