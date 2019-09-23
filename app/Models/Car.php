<?php

namespace App\Models;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Model;

class Car extends BaseModel
{
    protected $fillable = [
        'number',
        'trailerNumber',
        'length',
        'height',
        'width',
        'maxCubage',
        'maxWeight',
        'serial'
    ];

    protected $casts =[
      'maxWeight' => 'double',
      'maxCubage' => 'double',
    ];

    public function fuelConsumptions()
    {
        return $this->belongsToMany(FuelConsumption::class);
    }

    public function trips(){
        return $this->hasMany(Trip::class, 'carId');
    }
}
