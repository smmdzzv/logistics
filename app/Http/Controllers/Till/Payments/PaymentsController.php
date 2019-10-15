<?php

namespace App\Http\Controllers\Till\Payments;

use App\Http\Controllers\BaseController;
use App\Models\Branch;
use App\Models\LegalEntities\LegalEntity;
use App\Models\Till\Payment;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

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
        $account = LegalEntity::first()->accounts()->with('currency')->first();
        return view('till.payments.index', compact('branches', 'account'));
    }

    public function all()
    {
        return Payment::with('accountTo', 'payer', 'currency', 'paymentItem')->latest()
            ->paginate($this->pagination());
    }

    public function filtered()
    {
        $query = Payment::with('accountTo', 'payer', 'currency', 'paymentItem')->latest();

        $type = request()->input('type');
        $branch = request()->input('branch');

        if ($type)
            $query->whereHas('paymentItem', function (Builder $query) use ($type) {
                $query->where('type', $type);
            });

        if ($branch)
            $query->whereHas('branch', function (Builder $query) use ($branch) {
                $query->where('id', $branch);
            });

        return $query->paginate($this->pagination());
    }
}
