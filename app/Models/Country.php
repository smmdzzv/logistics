<?php

namespace App\Models;

use App\Branch;
use Illuminate\Database\Eloquent\Model;

class Country extends BaseModel
{
    public function Branches(){
        return $this->hasMany(Branch::class, 'country');
    }
}
