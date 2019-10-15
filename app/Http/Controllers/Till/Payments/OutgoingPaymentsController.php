<?php

namespace App\Http\Controllers\Till\Payments;

use App\Http\Requests\OutgoingPaymentRequest;
use App\Http\Requests\Till\PaymentRequest;
use App\Models\Currency;
use App\Models\LegalEntities\LegalEntity;
use App\Models\Order;
use App\Models\Till\MoneyExchange;
use App\Models\Till\Payment;
use App\Models\Till\PaymentItem;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class OutgoingPaymentsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('roles.allow:cashier, manager');
    }

    public function create(){
        $accountFrom = LegalEntity::first()->accounts()->with('currency')->first();
        $currencies = Currency::all();
        $paymentItems =  PaymentItem::where('type', 'out')->get();
        return view('till.payments.outgoing.create', compact('accountFrom', 'currencies', 'paymentItems'));
    }

    public function store(OutgoingPaymentRequest $request)
    {
        $accountFrom = LegalEntity::first()->accounts()->with('currency')->first();
        $payment = Payment::create([
            'branchId' => auth()->user()->branch->id,
            'cashierId' => auth()->id(),
            'currencyId' => $accountFrom->currency->id,
            'paymentItemId' => $request->input('paymentItemId'),
            'accountFromId' => $accountFrom->id,
            'amount' => round($request->input('amount'), 2)
        ]);

        //Change balance of account to
        $sum = $payment->amount;

        $payment->accountFrom->balance -= $payment->amount;;
        $payment->accountFrom->save();

        return redirect()->route('payments.index');
    }
}
