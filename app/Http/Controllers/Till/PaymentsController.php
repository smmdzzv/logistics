<?php


namespace App\Http\Controllers\Till;


use App\Http\Controllers\Controller;
use App\Models\Currency;
use App\Models\LegalEntities\LegalEntity;

class PaymentsController extends Controller
{
    public function create(){
        $accountTo = LegalEntity::first()->accounts()->first();
        $currencies = Currency::all();
        return view('till.payments.create', compact('accountTo', 'currencies'));
    }
}
