<?php

namespace App\Http\Controllers\Orders;

use App\Models\Users\Client;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class OrderPaymentsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('roles.deny:client');
    }

    public function unpaid(Client $client){
        return $client->unpaidOrders;
    }
}
