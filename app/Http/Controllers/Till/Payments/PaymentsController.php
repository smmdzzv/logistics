<?php

namespace App\Http\Controllers\Till\Payments;

use App\Http\Controllers\BaseController;
use App\Models\Branch;
use App\Models\Currency;
use App\Models\LegalEntities\LegalEntity;
use App\Models\Till\Payment;
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
        $account = LegalEntity::first()->accounts()->with('currency')->first();
        return view('till.payments.index', compact('branches', 'account', 'currencies'));
    }

    public function all()
    {
        return Payment::with('accountTo', 'cashier', 'payer', 'currency', 'paymentItem')->latest()
            ->paginate($this->pagination());
    }

    public function filtered()
    {
        $query = Payment::with('accountTo', 'cashier', 'payer', 'currency', 'paymentItem')->latest();

        $type = request('type');
        $branch = request('branch');
        $user = request('user');
        $currency = request('currency');
        $from = request('from');
        $to = request('to');

        if ($type)
            $query->whereHas('paymentItem', function (Builder $query) use ($type) {
                $query->where('type', $type);
            });

        if ($branch)
            $query->whereHas('branch', function (Builder $query) use ($branch) {
                $query->where('id', $branch);
            });

        if($user)
            $query->whereHas('payer', function (Builder $query) use ($user) {
                $query->where('id', $user);
            });
        if($currency)
            $query->whereHas('currency', function (Builder $query) use ($currency) {
                $query->where('id', $currency);
            });
        if($from)
            $query->where('created_at','>', Carbon::createFromDate($from));
        if($to)
            $query->where('created_at','<', Carbon::createFromDate($to)->addDay());
        return $query->paginate($this->pagination());
    }
}
