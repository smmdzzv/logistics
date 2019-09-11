<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class FuelConsumption extends Model
{
    public function car(){
        return $this->belongsToMany(Car::class);
    }
}
