<?php

namespace App\Http\Controllers\Till\Payments;

use App\Data\Filters\PaymentFilter;
use App\Models\Branch;
use App\Models\Till\Payment;
use App\Http\Controllers\Controller;

class PendingPaymentsController extends Controller
{
    public function index(){
        $branches = Branch::all();
        return view('till.payments.pending.index', compact('branches'));
    }

    public function filtered(){
        $query =  Payment::with('accountTo', 'cashier', 'payer', 'currency', 'paymentItem')
            ->where('status', 'pending')
            ->latest();

        $filter = new PaymentFilter(request()->all(), $query);
        $query = $filter->filter();

        return $query->paginate(20);
    }
}
