<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\Role;
use Illuminate\Validation\Rule;

class DriversController extends AbstractRoleUsersController
{
    protected function getRoleName()
    {
        return 'driver';
    }
}
