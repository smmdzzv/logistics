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

    public function getStatistics($clientCode)
    {
        $client = Client::where('code', $clientCode)->first();
        if(!$client)
            abort(400, 'Клиент не найден');
        if (request()->get('dateFrom') && request()->get('dateTo'))
            return $client->getOrdersStatistics(request()->get('dateFrom'), request()->get('dateTo'));
        else abort(400, 'Необходимо указать дату начала и конца периода');
    }
}
