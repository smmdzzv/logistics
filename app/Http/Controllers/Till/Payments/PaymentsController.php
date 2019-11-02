<?php

namespace App\Http\Controllers\Till\Payments;

use App\Http\Controllers\BaseController;
use App\Models\Branch;
use App\Models\Currency;
use App\Models\Till\Payment;
use App\Models\Till\PaymentItem;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;

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
        return Payment::with('accountTo', 'cashier', 'payer', 'currency', 'paymentItem')->latest()
            ->paginate($this->pagination());
    }

    public function filtered()
    {
        $query = Payment::with('accountTo', 'cashier', 'payer', 'currency', 'paymentItem')
            ->where('status', 'completed')
            ->latest();

        $type = request('type');
        $item = request('item');
        $branch = request('branch');
        $user = request('user');
        $currency = request('currency');
        $from = request('from');
        $to = request('to');
        $cashier = request('cashier');
        $min = request('min');
        $max = request('max');

        if ($item)
            $query->whereHas('paymentItem', function (Builder $query) use ($item) {
                $query->where('id', $item);
            });
        else if ($type)
            $query->whereHas('paymentItem', function (Builder $query) use ($type) {
                $query->where('type', $type);
            });

        if ($branch)
            $query->whereHas('branch', function (Builder $query) use ($branch) {
                $query->where('id', $branch);
            });

        if ($user)
            $query->whereHas('payer', function (Builder $query) use ($user) {
                $query->where('id', $user);
            });

        if ($currency)
            $query->whereHas('currency', function (Builder $query) use ($currency) {
                $query->where('id', $currency);
            });

        if ($from)
            $query->where('created_at', '>', Carbon::createFromDate($from));

        if ($to)
            $query->where('created_at', '<', Carbon::createFromDate($to)->addDay());

        if ($cashier)
            $query->whereHas('cashier', function (Builder $query) use ($cashier) {
                $query->where('id', $cashier);
            });

        if ($min)
            $query->where('amount', '>=', $min);

        if ($max)
            $query->where('amount', '<=', $max);

        return $query->paginate($this->pagination());
    }
}
