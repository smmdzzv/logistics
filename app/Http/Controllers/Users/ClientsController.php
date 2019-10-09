<?php

namespace App\Http\Controllers\Users;

use App\Models\Users\Client;

class ClientsController extends AbstractRoleUsersController
{

    public function __construct()
    {
        $this->entityClass = Client::class;
    }

    public function index(){
        $url = 'concrete/client/all';
        $title = 'Список клиентов';
        return view('users.index', compact('url', 'title'));
    }

    //TODO handle input params in router
    public function orders(){
        $client = Client::findOrFail(request()->get('client'));
        return $client->orders;
    }
}
