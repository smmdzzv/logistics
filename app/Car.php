<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Car extends Model
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

    public function fuelConsumptions()
    {
        return $this->belongsToMany(FuelConsumption::class);
    }
}
