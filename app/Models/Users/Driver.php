<?php

namespace App\Models\Users;

use App\Models\Trip;
use App\User;

class Driver extends User
{
    public function trip(){
        return $this->hasMany(Trip::class, 'driverId');
    }
}
