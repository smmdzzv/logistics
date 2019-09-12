<?php

namespace App\Models;

use App\Branch;
use Illuminate\Database\Eloquent\Model;

class Country extends Model
{
    public function Branches(){
        return $this->hasMany(Branch::class, 'country');
    }
}
