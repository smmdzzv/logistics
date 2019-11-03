<?php

namespace App\Http\Controllers\Till\Payments;

use App\Data\RequestWriters\Payments\OutgoingPaymentRequestWriter;
use App\Http\Controllers\Controller;
use App\Http\Requests\OutgoingPaymentRequest;
use App\Models\Currency;
use App\Models\LegalEntities\LegalEntity;
use App\Models\Till\Payment;
use App\Models\Till\PaymentItem;
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
        $accountsFrom = LegalEntity::first()->accounts()->with('currency')->get();
        $currencies = Currency::all();
        $paymentItems = PaymentItem::where('type', 'out')->get();
        return view('till.payments.outgoing.create', compact('accountsFrom', 'currencies', 'paymentItems'));
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

    public function edit(Payment $payment)
    {
        if ($payment->paymentItem->type !== 'out')
            abort(403, 'Указанный платеж не является исходящим');
        $payment->load( 'accountFrom.currency', 'currency');

        return view('till.payments.outgoing.edit', compact('payment'));
    }

    public function update(OutgoingPaymentRequest $request, Payment $payment){
        if($payment->status === 'completed')
            abort(403, 'Проведенные операции нельзя редактировать');

        $data = new stdClass();
        $data->payment = $request->all();
        $data->branchId = auth()->user()->branch->id;
        $data->cashierId = auth()->id();

        $paymentWriter = new OutgoingPaymentRequestWriter($data);
        $paymentWriter->write();

        return;
    }
}
