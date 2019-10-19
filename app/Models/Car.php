<?php

namespace App\Models;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Model;

class Car extends BaseModel
{
    protected $guarded = [];

    protected $casts =[
      'maxWeight' => 'double',
      'maxCubage' => 'double',
    ];

    public function fuelConsumptions()
    {
        return $this->hasMany(FuelConsumption::class);
    }

    public function trips(){
        return $this->hasMany(Trip::class, 'carId');
    }
}
