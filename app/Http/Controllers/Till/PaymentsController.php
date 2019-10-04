<?php


namespace App\Http\Controllers\Till;


use App\Http\Controllers\Controller;
use App\Models\Currency;
use App\Models\LegalEntities\LegalEntity;
use App\Models\Till\PaymentItem;

class PaymentsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:cashier, manager');
    }

    public function create(){
        $accountTo = LegalEntity::first()->accounts()->with('currency')->first();
        $currencies = Currency::all();
        return view('till.payments.create', compact('accountTo', 'currencies'));
    }
}
