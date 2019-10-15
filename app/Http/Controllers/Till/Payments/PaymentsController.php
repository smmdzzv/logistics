<?php

namespace App\Http\Controllers\Till\Payments;

use App\Models\Branch;
use App\Models\LegalEntities\LegalEntity;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class PaymentsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('roles.allow:cashier,director');
    }

    public function index(){
        $branches = Branch::all();
        $account = LegalEntity::first()->accounts()->with('currency')->first();
        return view('till.payments.index', compact('branches', 'account'));
    }
}
