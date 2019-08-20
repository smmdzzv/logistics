<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Car extends Model
{
    public function fuelConsumptions(){
        return $this->hasMany(FuelConsumption::class);
    }
}
