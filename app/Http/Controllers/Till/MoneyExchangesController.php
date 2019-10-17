<?php

namespace App\Http\Controllers\Till;

use App\Models\Currency;
use App\Models\Till\MoneyExchange;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class MoneyExchangesController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');

        $this->middleware('roles.deny:client');
    }

    private function rules(){
        return [
            'from' => 'required|exists:currencies,id',
            'to' => 'required|exists:currencies,id|different:from',
            'coefficient' => 'required|numeric'
        ];
    }

//    public function index()
//    {
//        //
//    }

    public function exchangeRate($from, $to){
            return MoneyExchange::with('toCurrency')->where('from',$from)->where('to',$to)->firstOrFail();
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
}
