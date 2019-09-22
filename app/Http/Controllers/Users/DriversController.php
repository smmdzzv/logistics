<?php

namespace App\Http\Controllers\Users;

class DriversController extends AbstractConcreteUsersController
{
    protected function getRoleName()
    {
        return 'driver';
    }

    protected function getClassName()
    {
       return 'App\Models\Users\Driver';
    }

    protected function getRoleNamePlural()
    {
        return 'drivers';
    }
}
