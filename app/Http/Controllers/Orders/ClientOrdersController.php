<?php

namespace App\Http\Controllers\Orders;

use App\Http\Controllers\Controller;
use App\Models\Users\Client;

class ClientOrdersController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('roles.deny:client');
    }

    public function unpaid(Client $client)
    {
        return $client->unpaidOrders;
    }

    public function totalDebt(Client $client)
    {
        return $client->orders()
            ->whereDoesntHave('payment')
            ->sum('totalPrice');
    }
}
