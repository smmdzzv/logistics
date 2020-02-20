<?php


namespace App\Http\Controllers\Till\Accounts;


use App\Http\Controllers\BaseController;
use App\Models\Till\Account;

class AccountsController extends BaseController
{
    public function __construct()
    {
        $this->middleware('auth');

        $this->middleware('roles.allow:admin, cashier, manager');
    }

    public function holderAccounts($holder)
    {
        return Account::with('currency')->where('owner_id', $holder)->get();
    }
}
