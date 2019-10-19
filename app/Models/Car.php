<?php

namespace App\Models;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Car extends BaseModel
{
    protected $guarded = [];

    protected $casts = [
        'maxWeight' => 'double',
        'maxCubage' => 'double',
        'length' => 'double',
        'height' => 'double',
        'width' => 'double',
        'trailerMaxCubage' => 'double',
        'trailerMaxWeight' => 'double',
    ];

    public function toChinaConsumption()
    {
        return $this->fuelConsumption()
            ->latest()
            ->whereHas('destination', function (Builder $query) {
                $query->where('name', '=', 'Китай');
            });
    }

    public function fuelConsumptions()
    {
        return $this->hasMany(FuelConsumption::class);
    }


    public function fuelConsumption()
    {
        return $this->hasOne(FuelConsumption::class)->latest();
    }

    public function fromChinaConsumption()
    {
        return $this->fuelConsumption()
            ->latest()
            ->whereHas('destination', function (Builder $query) {
                $query->where('name', '=', 'Таджикистан');
            });
    }

    public function trips()
    {
        return $this->hasMany(Trip::class, 'carId');
    }
}
