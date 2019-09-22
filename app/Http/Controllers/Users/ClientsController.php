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
}
