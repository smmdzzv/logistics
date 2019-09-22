<?php

namespace App\Http\Controllers\Users;

class ClientsController extends AbstractConcreteUsersController
{
    protected function getRoleName()
    {
        return 'client';
    }

    protected function getClassName()
    {
       return 'App\Models\Users\Client';
    }

    protected function getRoleNamePlural()
    {
        return 'clients';
    }

    public function index(){
        $url = 'concrete/client/all';
        $title = 'Список клиентов';
        return view('users.index', compact('url', 'title'));
    }
}
