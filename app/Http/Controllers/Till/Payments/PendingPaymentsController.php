<?php

namespace App\Http\Controllers\Till\Payments;

use App\Models\Branch;
use App\Models\Till\Payment;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class PendingPaymentsController extends Controller
{
    public function index(){
        $branches = Branch::all();
        return view('till.payments.pending.index', compact('branches'));
    }

    public function all(){
        return Payment::where('status', 'pending')->get();
    }
}
