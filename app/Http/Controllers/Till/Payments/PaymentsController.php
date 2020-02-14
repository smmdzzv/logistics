<?php

namespace App\Http\Controllers\Till\Payments;

use App\Data\Filters\PaymentFilter;
use App\Http\Controllers\BaseController;
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
        return Payment::with('accountTo', 'cashier', 'payer', 'currency', 'paymentItem', 'recipient')
            ->latest()
            ->paginate($this->pagination());
    }

    public function create(){
        $branches = $this->getBranches();
        $currencies = Currency::all();
        $paymentItems = PaymentItem::all();
        return view('till.payments.create', compact('branches', 'currencies', 'paymentItems'));
    }

    public function show(Payment $payment){
        $payment->load(
            'cashier',
            'preparedBy',
            'payer',
            'currency',
            'paymentItem',
            'branch',
            'accountFrom',
            'accountTo',
            'exchange',
            'orderPaymentItems.storedItem.info.billingInfo',
            'orderPaymentItems.storedItem.info.item');
        return view('till.payments.show', compact('payment'));
    }

    public function filtered()
    {
        $query = Payment::with('accountTo', 'cashier', 'payer', 'currency', 'paymentItem', 'recipient')
            ->where('status', 'completed')
            ->latest();

        $filter = new PaymentFilter(request()->all(), $query);
        $query = $filter->filter();

        return $query->paginate($this->pagination());
    }
}
