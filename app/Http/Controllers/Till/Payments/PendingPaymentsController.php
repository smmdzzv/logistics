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
}
