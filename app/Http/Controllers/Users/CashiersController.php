<?php


namespace App\Http\Controllers\Users;


use App\Models\Users\Cashier;

class CashiersController extends AbstractRoleUsersController
{
    public function __construct()
    {
        $this->entityClass = Cashier::class;
    }

    public function index()
    {
        $url = 'concrete/cashier/all';
        $title = 'Список кассиров';
        return view('users.index', compact('url', 'title'));
    }
}
