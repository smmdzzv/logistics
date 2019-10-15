<?php

namespace App\Models;

use App\Branch;
use Illuminate\Database\Eloquent\Model;

class Country extends BaseModel
{
    public function branches(){
        return $this->hasMany(Branch::class, 'country');
    }

    public function currencies(){
        return $this->hasMany(Currency::class);
    }
}
