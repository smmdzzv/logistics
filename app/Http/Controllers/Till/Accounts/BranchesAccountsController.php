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
        $branches = $this->getBranches();
        $branches->load('accounts.currency');
        return view('accounts.branches.index', compact('branches'));
    }
}
