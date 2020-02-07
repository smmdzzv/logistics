<?php

namespace App\Http\Controllers\Till\Accounts;

use App\Http\Controllers\BaseController;
use App\Models\Branch;

class BranchesAccountsController extends BaseController
{

    public function __construct()
    {
        $this->middleware('auth');

        $this->middleware('roles.allow:admin, cashier, manager');

        $this->middleware('user.branch');
    }

    public function index(){
        $branches = [];
        if(auth()->user()->hasRole('admin'))
            $branches = Branch::all();
        else
            $branches[] = auth()->user()->branch;
        return view('accounts.branches.index', compact('branches'));
    }
}
