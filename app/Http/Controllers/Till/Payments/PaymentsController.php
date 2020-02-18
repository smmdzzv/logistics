<?php

namespace App\Http\Controllers\Till\Payments;

use App\Data\Filters\PaymentFilter;
use App\Data\RequestWriters\Payments\PaymentRequestWriter;
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
        $this->middleware('roles.allow:cashier,director');
    }

    public function index()
    {
        $branches = Branch::all();
        $currencies = Currency::all();
//        $account = LegalEntity::first()->accounts()->with('currency')->first();
        $paymentItems = PaymentItem::all();
        return view('till.payments.index', compact('branches', 'currencies', 'paymentItems'));
    }

    public function all()
    {
        return Payment::without(
            'payerAccount',
            'payeeAccount',
            'exchangeRate')
            ->latest()
            ->paginate($this->pagination());
    }

    public function create()
    {
        $branches = $this->getBranches();
        $currencies = Currency::all();
        $paymentItems = PaymentItem::public()->get();
        return view('till.payments.create', compact('branches', 'currencies', 'paymentItems'));
    }

    public function store(PaymentRequest $request)
    {
        $writer = new PaymentRequestWriter($request);
        $payment = $writer->write();
        return $payment->id;
    }

    public function show(Payment $payment)
    {
        $payment->load(
            'orderPaymentItems.storedItem.info.billingInfo',
            'orderPaymentItems.storedItem.info.item', 'exchangeRate.fromCurrency', 'exchangeRate.toCurrency');
        return view('till.payments.show', compact('payment'));
    }

    public function filtered()
    {

        $query = Payment::without(
            'payerAccount',
            'payeeAccount',
            'exchangeRate'
        )
            ->where('status', 'completed')
            ->latest();

        $filter = new PaymentFilter(request()->all(), $query);
        $query = $filter->filter();

        return $query->paginate($this->pagination());
    }
}
