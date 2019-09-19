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

    public function fuelConsumptions()
    {
        return $this->belongsToMany(FuelConsumption::class);
    }
}
