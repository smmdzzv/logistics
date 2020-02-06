<?php

namespace App\Models\Users;

use App\Models\Trip;
use App\User;

class Storekeeper extends Employee
{
    public function getRoles()
    {
        return [
            'storekeeper'
        ];
    }
}
