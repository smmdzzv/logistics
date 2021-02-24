<?php

namespace App\Http\Controllers\Till\Payments;

use App\Data\Filters\PaymentFilter;
use App\Data\Reports\CashReport;
use App\Data\RequestWriters\Payments\PaymentRequestWriterFabric;
use App\Http\Controllers\BaseController;
use App\Http\Requests\Till\PaymentRequest;
use App\Models\Branch;
use App\Models\Currency;
use App\Models\Till\Payment;
use App\Models\Till\PaymentItem;
use App\Services\Till\Payment\PaymentService;

class PaymentsController extends BaseController
{
    private PaymentService $paymentService;

    public function __construct(PaymentService $paymentService)
    {
        $this->middleware('auth');
        $this->middleware('roles.deny:client')->except('destroy');
        $this->middleware('roles.allow:admin')->only(['destroy']);

        $this->paymentService = $paymentService;
    }

    public function index()
    {
        $branches = Branch::all();
        $currencies = Currency::all();
        $paymentItems = PaymentItem::all();
        return view('till.payments.index', compact('branches', 'currencies', 'paymentItems'));
    }

    public function create()
    {
        $branches = $this->getBranches();
        $currencies = Currency::all();
        $paymentItems = PaymentItem::public()->get();
        return view('till.payments.create', compact('branches', 'currencies', 'paymentItems'));
    }

    public function storeOrUpdate(PaymentRequest $request)
    {
        $fabric = new PaymentRequestWriterFabric($request->all());
        $writer = $fabric->getWriter();
        $payment = $writer->write();
        return $payment->id;
    }

    public function edit(Payment $payment)
    {
        if ($payment->status === 'completed')
            abort(403, 'Редактирование проведенных платежей запрещено');

        $branches = $this->getBranches();
        $currencies = Currency::all();
        $paymentItems = PaymentItem::public()->get();
        return view('till.payments.edit', compact('branches', 'currencies', 'paymentItems', 'payment'));
    }

    public function show($payment)
    {
        $payment = Payment::withTrashed()
            ->with(
                'clientItemsSelection.storedItems.info.billingInfo',
                'clientItemsSelection.storedItems.info.item',
                'exchangeRate.fromCurrency',
                'exchangeRate.toCurrency',
                'relatedPayment',
                'relatedPayments'
            )
            ->find($payment);
        return view('till.payments.show', compact('payment'));
    }

    public function destroy(Payment $payment)
    {
        $this->paymentService->destroy($payment);
    }
}
