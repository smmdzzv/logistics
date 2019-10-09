<?php

namespace App\Models\Users;

use App\Models\Trip;
use App\User;

class Driver extends Employee
{
    public function getRoles()
    {
        return [
            'driver'
        ];
    }

    public function trips()
    {
        return $this->hasMany(Trip::class, 'driverId');
    }
}
