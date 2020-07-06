<?php
/**
 *
 * @author Sultonazar Mamadazizov <sultonazar.mamadazizov@mail.ru> on 06.07.2020
 */

namespace App\Http\Controllers\Orders;


use App\Http\Controllers\Controller;
use App\Models\Users\Client;

class ClientActiveOrdersController extends Controller
{
    public function index(Client $client)
    {
        return $client->activeOrders;
    }
}
