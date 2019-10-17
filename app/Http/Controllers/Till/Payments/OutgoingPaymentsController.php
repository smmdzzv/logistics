<?php

namespace App\Http\Controllers\Till\Payments;

use App\Data\RequestWriters\Payments\OutgoingPaymentRequestWriter;
use App\Http\Requests\OutgoingPaymentRequest;
use App\Models\Currency;
use App\Models\LegalEntities\LegalEntity;
use App\Models\Till\PaymentItem;
use App\Http\Controllers\Controller;
use stdClass;

class OutgoingPaymentsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('roles.allow:cashier, manager');
    }

    public function create()
    {
        $accountFrom = LegalEntity::first()->accounts()->with('currency')->first();
        $currencies = Currency::all();
        $paymentItems = PaymentItem::where('type', 'out')->get();
        return view('till.payments.outgoing.create', compact('accountFrom', 'currencies', 'paymentItems'));
    }

    public function store(OutgoingPaymentRequest $request)
    {
        $data = new stdClass();
        $data->payment = $request->all();
        $data->branchId = auth()->user()->branch->id;
        $data->cashierId = auth()->id();

        $paymentWriter = new OutgoingPaymentRequestWriter($data);
        $paymentWriter->write();

        return redirect()->route('payments.index');
    }
}
