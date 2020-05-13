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

class PaymentsController extends BaseController
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('roles.allow:cashier,manager')->except(['show']);
        $this->middleware('roles.deny:client,driver')->only(['show']);
    }

    public function index()
    {
        $branches = Branch::all();
        $currencies = Currency::all();
        $paymentItems = PaymentItem::all();
        return view('till.payments.index', compact('branches', 'currencies', 'paymentItems'));
    }

//    public function all()
//    {
//        return Payment::without(
//            'payerAccount',
//            'payeeAccount',
//            'exchangeRate')
//            ->latest()
//            ->paginate($this->pagination());
//    }

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

    public function show(Payment $payment)
    {
        $payment->load(
            'orderPaymentItems.storedItem.info.billingInfo',
            'orderPaymentItems.storedItem.info.item',
            'exchangeRate.fromCurrency',
            'exchangeRate.toCurrency',
            'relatedPayment',
            'relatedPayments');
        return view('till.payments.show', compact('payment'));
    }

    public function filtered()
    {

        $query = Payment::without(
            'payerAccount',
            'payeeAccount',
            'exchangeRate'
        )->latest();

        $filter = new PaymentFilter(request()->all(), $query);
        $query = $filter->filter();

        $report = null;

        if (request()->get('calculateCash') === 'true'
            && request()->get('branchPayer')) {
            $report = new CashReport($query, request()->get('branchPayer'));
            $report->formReport();
            $report->convertToISONameKey();
        }

        $paginator = $query->paginate($this->pagination());
        $paginator['cashReport'] = $report ? $report->toArray() : null;
        return $paginator;
    }
}
