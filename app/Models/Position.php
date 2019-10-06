<?php

namespace App\Models;

use App\User;

class Position extends BaseModel
{
    protected $guarded = [];

    public function users(){
        return $this->hasMany(User::class);
    }
}
