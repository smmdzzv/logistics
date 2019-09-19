<?php

namespace App\Models;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Model;

class FuelConsumption extends BaseModel
{
    public function car(){
        return $this->belongsToMany(Car::class);
    }
}
